<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Database configuration - CHANGE THESE VALUES
$servername = "localhost";
$username = "root";          // Your DB username
$password = "";              // Your DB password
$dbname = "codelearn_db";       // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Set header first
header('Content-Type: application/json; charset=utf-8');

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$userId = $_SESSION['user_id'];
$userPlan = isset($_SESSION['plan']) ? $_SESSION['plan'] : 'free';

// Check if user is Pro or Max
if ($userPlan !== 'pro' && $userPlan !== 'team') {
    echo json_encode(['success' => false, 'message' => 'Certificate generation is available for Pro and Max members only']);
    exit();
}

// Get POST data
$action = isset($_POST['action']) ? $_POST['action'] : '';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$courseId = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

if ($action !== 'generate') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

// Validate inputs
if (empty($name) || strlen($name) < 2) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid name']);
    exit();
}

if ($courseId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please select a course']);
    exit();
}

// Check if course exists
$stmt = $conn->prepare("SELECT course_id, title FROM courses WHERE course_id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit();
}

$stmt->bind_param("i", $courseId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Course not found']);
    exit();
}

$course = $result->fetch_assoc();
$stmt->close();

// Check if certificate already exists
$stmt = $conn->prepare("SELECT certificate_id FROM certificates WHERE user_id = ? AND course_id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit();
}

$stmt->bind_param("ii", $userId, $courseId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Certificate already generated for this course']);
    exit();
}
$stmt->close();

// Generate certificate number
$currentYear = date('Y');
$certNumber = 'CL-' . $currentYear . '-' . str_pad($userId, 5, '0', STR_PAD_LEFT) . '-' . str_pad($courseId, 3, '0', STR_PAD_LEFT);

// Insert certificate
$stmt = $conn->prepare("INSERT INTO certificates (user_id, course_id, certificate_number, issued_date) VALUES (?, ?, ?, NOW())");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit();
}

$stmt->bind_param("iis", $userId, $courseId, $certNumber);

if ($stmt->execute()) {
    // Update user's session name if changed
    if ($name !== $_SESSION['name']) {
        $_SESSION['name'] = $name;
        
        // Update in database
        $updateStmt = $conn->prepare("UPDATE users SET name = ? WHERE user_id = ?");
        if ($updateStmt) {
            $updateStmt->bind_param("si", $name, $userId);
            $updateStmt->execute();
            $updateStmt->close();
        }
    }
    
    $stmt->close();
    $conn->close();
    echo json_encode([
        'success' => true, 
        'message' => 'Certificate generated successfully!',
        'certificate_number' => $certNumber
    ]);
} else {
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Failed to generate certificate: ' . $stmt->error]);
}
?>