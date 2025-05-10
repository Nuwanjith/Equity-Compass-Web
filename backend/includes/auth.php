<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

// Optional: Verify user still exists in database
require_once __DIR__ . '/../config/db_config.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        // User no longer exists
        session_destroy();
        header('Location: /login.php');
        exit();
    }
} catch(PDOException $e) {
    // Log error but don't prevent access
    error_log("Auth verification error: " . $e->getMessage());
}
?>