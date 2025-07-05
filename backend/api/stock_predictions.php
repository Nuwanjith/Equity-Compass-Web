<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Database configuration
require_once __DIR__ . '/../config/db_config.php';

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get and sanitize company parameter
    $companyCode = isset($_GET['company']) ? $conn->real_escape_string(strtoupper($_GET['company'])) : 'TYRE';
    
    // Validate company code
    $validCompanies = ['KCAB', 'TYRE', 'SAMP'];
    if (!in_array($companyCode, $validCompanies)) {
        throw new Exception("Invalid company code. Allowed values: KCAB, TYRE, SAMP");
    }

    // Prepare statement to get 30 most recent predictions
    $stmt = $conn->prepare("
        SELECT 
            `date` AS prediction_date,
            `xgboost_prediction` AS xgboost,
            `lstm_prediction` AS lstm,
            `ensembled_prediction` AS ensembled,
            `created_at`
        FROM `daily_predictions`
        WHERE `company_code` = ?
        ORDER BY `date` DESC
        LIMIT 30
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    // Bind parameters and execute
    $stmt->bind_param("s", $companyCode);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $predictions = [];
    
    while ($row = $result->fetch_assoc()) {
        // Calculate confidence score for each prediction
        $availableModels = 0;
        $totalScore = 0;
        
        if (!is_null($row['xgboost'])) {
            $availableModels++;
            $totalScore += $row['xgboost'];
        }
        
        if (!is_null($row['lstm'])) {
            $availableModels++;
            $totalScore += $row['lstm'];
        }
        
        if (!is_null($row['ensembled'])) {
            $availableModels++;
            $totalScore += $row['ensembled'];
        }
        
        $confidenceScore = $availableModels > 0 ? ($totalScore / $availableModels) : null;
        
        $predictions[] = [
            'prediction_date' => $row['prediction_date'],
            'xgboost' => $row['xgboost'] ? round($row['xgboost'], 2) : null,
            'lstm' => $row['lstm'] ? round($row['lstm'], 2) : null,
            'ensembled' => $row['ensembled'] ? round($row['ensembled'], 2) : null,
            'confidence_score' => $confidenceScore ? round($confidenceScore, 2) : null,
            'models_used' => $availableModels,
            'created_at' => $row['created_at']
        ];
    }
    
    if (!empty($predictions)) {
        // Success response with all predictions
        echo json_encode([
            'success' => true,
            'data' => $predictions,
            'company' => $companyCode,
            'time_generated' => date('Y-m-d H:i:s'),
            'count' => count($predictions)
        ], JSON_NUMERIC_CHECK);
    } else {
        // No prediction data found
        echo json_encode([
            'success' => false,
            'error' => "No prediction data found for company: $companyCode",
            'company' => $companyCode,
            'time_generated' => date('Y-m-d H:i:s')
        ]);
    }
    
} catch (Exception $e) {
    // Error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'company' => isset($companyCode) ? $companyCode : null,
        'time_generated' => date('Y-m-d H:i:s')
    ]);
} finally {
    if (isset($conn)) $conn->close();
}
?>