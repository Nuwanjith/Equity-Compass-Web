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
    
    // Query valuation metrics
    $sql = "SELECT 
                valuation_date,
                pe_ratio,
                pb_ratio,
                dividend_yield,
                ev_ebitda
            FROM company_valuations
            WHERE company_code = ?
            ORDER BY valuation_date DESC
            LIMIT 12"; // Last 12 valuations
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $companyCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $valuations = [];
    while ($row = $result->fetch_assoc()) {
        $valuations[] = [
            'date' => $row['valuation_date'],
            'pe_ratio' => round($row['pe_ratio'], 2),
            'pb_ratio' => round($row['pb_ratio'], 2),
            'dividend_yield' => round($row['dividend_yield'], 4),
            'ev_ebitda' => round($row['ev_ebitda'], 2)
        ];
    }
    
    // Successful response
    echo json_encode([
        'success' => true,
        'data' => $valuations,
        'company' => $companyCode,
        'time_generated' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    // [Same error handling as original]
}
?>