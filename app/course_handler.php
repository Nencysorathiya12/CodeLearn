<?php
session_start();
header('Content-Type: application/json');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codelearn_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        // Add new course
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $level = trim($_POST['level'] ?? '');
        $status = $_POST['status'] ?? 'active';
        
        if (empty($title) || empty($description)) {
            echo json_encode(['success' => false, 'message' => 'Title and description are required']);
            exit();
        }
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO courses (title, description, category, level, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            if ($stmt->execute([$title, $description, $category, $level, $status])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Course added successfully!',
                    'course_id' => $pdo->lastInsertId()
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add course']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        
    } elseif ($action === 'update') {
        // Update existing course
        $course_id = $_POST['course_id'] ?? 0;
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $level = trim($_POST['level'] ?? '');
        $status = $_POST['status'] ?? 'active';
        
        if (!$course_id || empty($title) || empty($description)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
            exit();
        }
        
        try {
            $stmt = $pdo->prepare("
                UPDATE courses 
                SET title = ?, description = ?, category = ?, level = ?, status = ?, updated_at = NOW()
                WHERE course_id = ?
            ");
            
            if ($stmt->execute([$title, $description, $category, $level, $status, $course_id])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Course updated successfully!'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update course']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        
    } elseif ($action === 'delete') {
        // Delete course
        $course_id = $_POST['course_id'] ?? 0;
        
        if (!$course_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid course ID']);
            exit();
        }
        
        try {
            // Check if course has lessons or quiz questions
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM lessons WHERE course_id = ?");
            $stmt->execute([$course_id]);
            $lessonCount = $stmt->fetchColumn();
            
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM quiz WHERE course_id = ?");
            $stmt->execute([$course_id]);
            $quizCount = $stmt->fetchColumn();
            
            if ($lessonCount > 0 || $quizCount > 0) {
                echo json_encode([
                    'success' => false, 
                    'message' => "Cannot delete course. It has $lessonCount lessons and $quizCount quiz questions. Please remove them first."
                ]);
                exit();
            }
            
            // Delete course
            $stmt = $pdo->prepare("DELETE FROM courses WHERE course_id = ?");
            
            if ($stmt->execute([$course_id])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Course deleted successfully!'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete course']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>