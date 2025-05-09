<?php
// Error reporting (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Config file path (adjust according to your structure)
$configPath = __DIR__ . '/../config/db_config.php';

// Verify config file exists
if (!file_exists($configPath)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database configuration not found',
        'debug' => [
            'current_directory' => __DIR__,
            'searched_path' => $configPath,
            'suggested_fix' => 'Adjust the path in stock_prices.php'
        ]
    ]);
    exit();
}

// Load config
require_once $configPath;

// Database connection
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Query for monthly averages
    $sql = "SELECT 
                DATE_FORMAT(trade_date, '%Y-%m') AS month,
                AVG(close_price) AS avg_price,
                SUM(volume) AS total_volume
            FROM kelani_tyre_stock_prices
            GROUP BY DATE_FORMAT(trade_date, '%Y-%m')
            ORDER BY month";

    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'month' => $row['month'],
            'avg_price' => round($row['avg_price'], 2),
            'volume' => (int)$row['total_volume']
        ];
    }

    // Successful response
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>