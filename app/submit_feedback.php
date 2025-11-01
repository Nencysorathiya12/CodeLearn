<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codelearn_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: contact.php?error=not_logged_in');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    
    // Validation
    if ($course_id <= 0) {
        header('Location: contact.php?error=course_required');
        exit();
    }
    
    if ($rating < 1 || $rating > 5) {
        header('Location: contact.php?error=invalid_rating');
        exit();
    }
    
    if (empty($comment)) {
        header('Location: contact.php?error=missing_fields');
        exit();
    }
    
    // Verify course exists
    $courseCheck = $conn->prepare("SELECT course_id FROM courses WHERE course_id = ?");
    $courseCheck->bind_param("i", $course_id);
    $courseCheck->execute();
    $courseResult = $courseCheck->get_result();
    
    if ($courseResult->num_rows === 0) {
        $courseCheck->close();
        $conn->close();
        header('Location: contact.php?error=invalid_course');
        exit();
    }
    $courseCheck->close();
    
    // Insert feedback
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, course_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiis", $user_id, $course_id, $rating, $comment);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: contact.php?success=1');
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header('Location: contact.php?error=submission_failed');
        exit();
    }
}

$conn->close();
header('Location: contact.php');
exit();
?>