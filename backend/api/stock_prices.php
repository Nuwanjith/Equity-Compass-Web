<?php
// Enable CORS if needed
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include db config using absolute path
require_once __DIR__ . '/../config/db_config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit();
}

// Set charset
$conn->set_charset("utf8");

// Fetch data
$sql = "SELECT trade_date, open_price, high_price, low_price, close_price, volume 
        FROM kelani_tyre_stock_prices 
        ORDER BY trade_date";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Query failed: ' . $conn->error
    ]);
    exit();
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$conn->close();

http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => $data
]);
exit();
?>