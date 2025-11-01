<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['attempt_id'])) {
    header('Location: index.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codelearn_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$attempt_id = $_POST['attempt_id'];
$course_id = $_POST['course_id'];
$user_id = $_SESSION['user_id'];

// Get all submitted answers
$score = 0;
$total_questions = 0;

foreach ($_POST as $key => $value) {
    if (strpos($key, 'question_') === 0) {
        $quiz_id = str_replace('question_', '', $key);
        $selected_option_id = $value;
        
        // Check if answer is correct
        $stmt = $pdo->prepare("SELECT is_correct FROM quiz_options WHERE option_id = ?");
        $stmt->execute([$selected_option_id]);
        $is_correct = $stmt->fetchColumn();
        
        if ($is_correct) {
            $score++;
        }
        
        // Save user answer
        $stmt = $pdo->prepare("INSERT INTO user_quiz_answers (attempt_id, quiz_id, selected_option_id, is_correct) VALUES (?, ?, ?, ?)");
        $stmt->execute([$attempt_id, $quiz_id, $selected_option_id, $is_correct]);
        
        $total_questions++;
    }
}

// Calculate percentage and pass status
$percentage = ($score / $total_questions) * 100;
$passed = $percentage >= 60;

// Calculate end time and update
$end_time = date('Y-m-d H:i:s');
$stmt = $pdo->prepare("UPDATE user_quiz_attempts SET score = ?, end_time = ?, passed = ? WHERE attempt_id = ?");
$stmt->execute([$score, $end_time, $passed, $attempt_id]);

// Redirect to results
header("Location: quiz_results.php?attempt_id=$attempt_id");
exit();
?>