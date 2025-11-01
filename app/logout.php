<?php
session_start();

// Check what type of user is logging out
$user_type = $_SESSION['user_type'] ?? 'user';

if ($user_type === 'admin') {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_email']);
} else {
    unset($_SESSION['user_id']);
    unset($_SESSION['name']);
    unset($_SESSION['email']);
}

unset($_SESSION['user_type']);
session_destroy();

// Just return success for AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit();
}

// For direct access, redirect to index
header('Location: index.php');
exit();
?>