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
    
    // Prepare statement
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
    
    // Bind parameters and execute
    $stmt->bind_param("s", $companyCode);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    if ($data) {
        // Success response with data
        echo json_encode([
            'success' => true,
            'data' => $data,
            'company' => $companyCode,
            'time_generated' => date('Y-m-d H:i:s')
        ], JSON_NUMERIC_CHECK);
    } else {
        // No data found for company
        echo json_encode([
            'success' => false,
            'error' => "No valuation data found for company: $companyCode",
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