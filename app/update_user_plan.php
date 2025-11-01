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
    $plan = $_POST['plan'] ?? null;
    
    if (!$user_id || !$plan || !in_array($plan, ['free', 'pro', 'team'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
        exit();
    }
    
    $stmt = $pdo->prepare("UPDATE users SET plan = ? WHERE user_id = ?");
    $stmt->execute([$plan, $user_id]);
    
    echo json_encode(['success' => true, 'message' => 'Plan updated successfully']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>