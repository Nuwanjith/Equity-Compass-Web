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
    
    // Validate company code (tickers with a trained xgboost_meta ensemble model)
    $validCompanies = ['DIPD', 'TYRE'];
    if (!in_array($companyCode, $validCompanies)) {
        throw new Exception("Invalid company code. Allowed values: " . implode(', ', $validCompanies));
    }

    // Prepare statement to get predictions grouped by month.
    // Final ensemble model is `xgboost_meta` (model_runs.model_type); its
    // per-day forecasts live in `predictions`, keyed by run_id.
    $stmt = $conn->prepare("
        SELECT
            DATE_FORMAT(p.`target_date`, '%Y-%m') AS month,
            AVG(p.`predicted_price`) AS avg_price,
            COUNT(*) AS volume,
            DATE_FORMAT(p.`target_date`, '%Y-%m') AS sort_key,
            DATE_FORMAT(p.`target_date`, '%b %Y') AS month_display
        FROM `predictions` p
        JOIN `model_runs` mr ON mr.`run_id` = p.`run_id`
        WHERE mr.`ticker` = ? AND mr.`model_type` = 'xgboost_meta'
        GROUP BY DATE_FORMAT(p.`target_date`, '%Y-%m'), DATE_FORMAT(p.`target_date`, '%b %Y')
        ORDER BY sort_key DESC
        LIMIT 12
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
        // `target_date` is already the actual future date the prediction is
        // forecasting (predicted_at + 30 days), so no shifting needed here.
        $predictions[] = [
            'month' => $row['month'],
            'avg_price' => round($row['avg_price'], 2),
            'volume' => (int)$row['volume'],
            'type' => 'prediction',
            'sort_key' => $row['sort_key'],
            'month_display' => $row['month_display']
        ];
    }
    
    if (!empty($predictions)) {
        // Success response with monthly predictions
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