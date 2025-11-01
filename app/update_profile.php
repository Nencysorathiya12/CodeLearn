<?php
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Database connection
$host = 'localhost';
$dbname = 'codelearn_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    
    $name = trim($_POST['name']);
    $user_id = $_SESSION['user_id'];
    
    // Validate name
    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Name cannot be empty']);
        exit;
    }
    
    if (strlen($name) < 2) {
        echo json_encode(['success' => false, 'message' => 'Name must be at least 2 characters']);
        exit;
    }
    
    if (strlen($name) > 50) {
        echo json_encode(['success' => false, 'message' => 'Name must be less than 50 characters']);
        exit;
    }
    
    try {
        // Update user name in database
        $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE user_id = ?");
        $stmt->execute([$name, $user_id]);
        
        // Update session
        $_SESSION['name'] = $name;
        
        echo json_encode([
            'success' => true, 
            'message' => 'Profile updated successfully!',
            'name' => $name
        ]);
        
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>