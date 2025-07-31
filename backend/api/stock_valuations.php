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
    $stockPricesTable = $companyCode . '_stock_prices';
    
    // Prepare statement for valuations
    $stmt = $conn->prepare("
        SELECT 
            `quarter`,
            `company`,
            `NAV-Based-valuation` AS nav_valuation,
            `EPS-Based-valuation` AS eps_valuation,
            `Graham-Number-valuation` AS graham_valuation,
            `created_at`
        FROM `valuations`
        WHERE `company` = ?
        ORDER BY `created_at` DESC
        LIMIT 1
    ");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    // Bind parameters and execute valuations query
    $stmt->bind_param("s", $companyCode);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    if ($data) {
        // Get current stock price
        $priceStmt = $conn->prepare("
            SELECT 
                `trade_date`,
                `close_price` AS current_price,
                `volume`
            FROM `$stockPricesTable`
            ORDER BY `trade_date` DESC
            LIMIT 1
        ");
        
        if (!$priceStmt) {
            throw new Exception("Prepare failed for stock prices: " . $conn->error);
        }
        
        if (!$priceStmt->execute()) {
            throw new Exception("Execute failed for stock prices: " . $priceStmt->error);
        }
        
        $priceResult = $priceStmt->get_result();
        $priceData = $priceResult->fetch_assoc();
        
        if ($priceData) {
            // Merge valuation and price data
            $data['current_price'] = $priceData['current_price'];
            $data['price_date'] = $priceData['trade_date'];
            $data['volume'] = $priceData['volume'];
        }
        
        // Success response with data
        $response = [
            'success' => true,
            'data' => $data,
            'company' => $companyCode,
            'time_generated' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($response, JSON_NUMERIC_CHECK);
    } else {
        // No data found for company
        $response = [
            'success' => false,
            'error' => "No valuation data found for company: $companyCode",
            'company' => $companyCode,
            'time_generated' => date('Y-m-d H:i:s')
        ];
        echo json_encode($response);
    }
    
} catch (Exception $e) {
    // Error response
    http_response_code(500);
    $response = [
        'success' => false,
        'error' => $e->getMessage(),
        'company' => isset($companyCode) ? $companyCode : null,
        'time_generated' => date('Y-m-d H:i:s')
    ];
    echo json_encode($response);
} finally {
    if (isset($conn)) $conn->close();
}
?>

{"success":false,"error":"No valuation data found for company: KCAB","company":"KCAB","time_generated":"2025-07-31 17:30:27"}