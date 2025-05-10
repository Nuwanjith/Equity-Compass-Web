<?php
session_start();
require_once __DIR__ . '/../backend/config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_username = $_POST['username'] ?? '';
    $input_password = $_POST['password'] ?? '';
    
    try {
        // Connect using privileged credentials from db_config
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check against users table
        $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = :username");
        $stmt->bindParam(':username', $input_username);
        $stmt->execute();
        
        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($input_password, $user['password_hash'])) {
                // Valid login - set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: /frontend/dashboard.php');
                exit;
            }
        }
        
        $error = "Invalid username or password";
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>