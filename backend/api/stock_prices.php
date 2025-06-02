<?php
// Error reporting (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

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

// Load config
require_once $configPath;

// Database connection
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get company code from query parameter
    $companyCode = isset($_GET['company']) ? strtoupper($_GET['company']) : 'TYRE';
    
    // Validate company code
    $validCompanies = ['KCAB', 'TYRE', 'SAMP'];
    if (!in_array($companyCode, $validCompanies)) {
        throw new Exception("Invalid company code. Allowed values: KCAB, TYRE, SAMP");
    }

    // Determine table name based on company code
    $tableName = $companyCode . '_stock_prices';

    // Check if table exists
    $checkTable = $conn->query("SHOW TABLES LIKE '$tableName'");
    if ($checkTable->num_rows == 0) {
        throw new Exception("Table not found for company code: $companyCode");
    }

    // Query for monthly averages (12 months historical data)
    $sql = "SELECT 
                DATE_FORMAT(trade_date, '%Y-%m') AS month,
                AVG(close_price) AS avg_price,
                SUM(volume) AS total_volume
            FROM $tableName
            GROUP BY DATE_FORMAT(trade_date, '%Y-%m')
            ORDER BY month DESC
            LIMIT 12";

    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    // Process historical data
    $historicalData = [];
    while ($row = $result->fetch_assoc()) {
        $historicalData[] = [
            'month' => $row['month'],
            'avg_price' => round($row['avg_price'], 2),
            'volume' => (int)$row['total_volume'],
            'type' => 'historical'
        ];
    }

    // Get prediction value
    $predictionQuery = "SELECT 
                        DATE_FORMAT(date, '%Y-%m') AS month,
                        ensembled_prediction AS avg_price
                      FROM Equity_compass_poc.daily_predictions
                      WHERE company_code = ?
                      ORDER BY date DESC
                      LIMIT 1";

    $stmt = $conn->prepare($predictionQuery);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $companyCode);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $predictionResult = $stmt->get_result();
    $predictionData = [];
    
    if ($predictionResult && $predictionResult->num_rows > 0) {
        $predictionRow = $predictionResult->fetch_assoc();
        $predictionData = [
            'month' => $predictionRow['month'],
            'avg_price' => round($predictionRow['avg_price'], 2),
            'volume' => null,
            'type' => 'prediction'
        ];
    }

    // Combine data
    $combinedData = array_merge($historicalData, [$predictionData]);

    // Successful response
    echo json_encode([
        'success' => true,
        'data' => $combinedData,
        'company' => $companyCode
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'company' => isset($companyCode) ? $companyCode : null
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>