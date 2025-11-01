<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codelearn_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $user_id = $_POST['user_id'] ?? null;
    
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
        exit();
    }
    
    // Delete user and related data
    $pdo->beginTransaction();
    
    // Delete related records first (to avoid foreign key issues)
    $pdo->prepare("DELETE FROM feedback WHERE user_id = ?")->execute([$user_id]);
    $pdo->prepare("DELETE FROM payment WHERE user_id = ?")->execute([$user_id]);
    
    // Delete user
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
    
} catch(PDOException $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>