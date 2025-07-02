<?php
// Config file path
$configPath = __DIR__ . '/../config/db_config.php';

// Verify config file exists
if (!file_exists($configPath)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database configuration not found'
    ]);
    exit();
}

require_once $configPath;

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Validate company code (same as original)
    
    // Query predictions
    $sql = "SELECT 
                prediction_date,
                next_month_prediction,
                next_quarter_prediction,
                next_year_prediction,
                confidence_score
            FROM company_predictions
            WHERE company_code = ?
            ORDER BY prediction_date DESC
            LIMIT 1"; // Most recent prediction
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $companyCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $prediction = $result->fetch_assoc();
    
    // Successful response
    echo json_encode([
        'success' => true,
        'data' => $prediction ? [
            'prediction_date' => $prediction['prediction_date'],
            'next_month' => round($prediction['next_month_prediction'], 2),
            'next_quarter' => round($prediction['next_quarter_prediction'], 2),
            'next_year' => round($prediction['next_year_prediction'], 2),
            'confidence' => round($prediction['confidence_score'], 2)
        ] : null,
        'company' => $companyCode,
        'time_generated' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    // [Same error handling as original]
}
?>