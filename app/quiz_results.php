<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['attempt_id'])) {
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

$attempt_id = $_GET['attempt_id'];

// Get quiz attempt details
$stmt = $pdo->prepare("
    SELECT uqa.*, c.title as course_title, 
           COALESCE(TIMESTAMPDIFF(SECOND, uqa.start_time, uqa.end_time), 0) as time_taken_seconds
    FROM user_quiz_attempts uqa
    JOIN courses c ON uqa.course_id = c.course_id
    WHERE uqa.attempt_id = ? AND uqa.user_id = ?
");
$stmt->execute([$attempt_id, $_SESSION['user_id']]);
$attempt = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$attempt) {
    die("Quiz attempt not found!");
}

// Get detailed answers
$stmt = $pdo->prepare("
    SELECT uqa.*, q.question, qo.option_text, qo.is_correct, correct_opt.option_text as correct_answer
    FROM user_quiz_answers uqa
    JOIN quiz q ON uqa.quiz_id = q.quiz_id
    JOIN quiz_options qo ON uqa.selected_option_id = qo.option_id
    LEFT JOIN quiz_options correct_opt ON correct_opt.quiz_id = q.quiz_id AND correct_opt.is_correct = 1
    WHERE uqa.attempt_id = ?
");
$stmt->execute([$attempt_id]);
$answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$percentage = ($attempt['score'] / $attempt['total_questions']) * 100;
$grade = $percentage >= 90 ? 'A' : ($percentage >= 80 ? 'B' : ($percentage >= 70 ? 'C' : ($percentage >= 60 ? 'D' : 'F')));

// Fix time calculation
$time_taken_seconds = max(0, intval($attempt['time_taken_seconds'])); // Ensure positive integer
$time_minutes = floor($time_taken_seconds / 60);
$time_seconds = $time_taken_seconds % 60;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - <?php echo htmlspecialchars($attempt['course_title']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .results-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .results-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 40px;
            color: white;
            text-align: center;
        }

        .completion-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounceIn 0.6s ease;
        }

        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .results-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .course-name {
            font-size: 18px;
            opacity: 0.9;
        }

        .results-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 40px;
            border-bottom: 2px solid #f0f0f0;
        }

        .summary-card {
            text-align: center;
            padding: 20px;
            border-radius: 12px;
        }

        .summary-card.score {
            background: linear-gradient(135deg, #fff3cd, #fff9e6);
        }

        .summary-card.grade {
            background: linear-gradient(135deg, #fff9c4, #fff176);
        }

        .summary-card.time {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        }

        .summary-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .summary-value {
            font-size: 48px;
            font-weight: 700;
            color: #333;
        }

        .summary-card.grade .summary-value {
            color: #f57c00;
        }

        .summary-card.time .summary-value {
            font-size: 36px;
        }

        .message-box {
            padding: 30px 40px;
            text-align: center;
            border-bottom: 2px solid #f0f0f0;
        }

        .message-box.pass {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .message-box.fail {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .message-box h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .detailed-results {
            padding: 40px;
        }

        .detailed-results h3 {
            font-size: 20px;
            margin-bottom: 25px;
            color: #333;
            font-weight: 600;
        }

        .question-result {
            margin-bottom: 25px;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #ccc;
        }

        .question-result.correct {
            background: #d4edda;
            border-color: #28a745;
        }

        .question-result.incorrect {
            background: #f8d7da;
            border-color: #dc3545;
        }

        .question-result-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .result-icon {
            font-size: 20px;
        }

        .result-icon.correct {
            color: #28a745;
        }

        .result-icon.incorrect {
            color: #dc3545;
        }

        .question-text {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            flex: 1;
        }

        .answer-info {
            font-size: 14px;
            margin-top: 10px;
        }

        .your-answer {
            color: #666;
            margin-bottom: 5px;
        }

        .correct-answer {
            color: #155724;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            padding: 40px;
            justify-content: center;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-retake {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-retake:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-close {
            background: #6c757d;
            color: white;
        }

        .btn-close:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .results-summary {
                grid-template-columns: 1fr;
                padding: 25px;
            }

            .results-header {
                padding: 30px 20px;
            }

            .detailed-results {
                padding: 25px;
            }

            .action-buttons {
                flex-direction: column;
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="results-container">
        <div class="results-header">
            <div class="completion-icon">🏆</div>
            <div class="results-title">Quiz Completed!</div>
            <div class="course-name"><?php echo htmlspecialchars($attempt['course_title']); ?> Quiz</div>
        </div>

        <div class="results-summary">
            <div class="summary-card score">
                <div class="summary-label">Score</div>
                <div class="summary-value"><?php echo round($percentage); ?>%</div>
            </div>
            <div class="summary-card grade">
                <div class="summary-label">Grade</div>
                <div class="summary-value"><?php echo $grade; ?></div>
            </div>
            <div class="summary-card time">
                <div class="summary-label">Time Taken</div>
                    <div class="summary-value">
                        <?php 
                        if ($time_taken_seconds > 0) {
                        echo $time_minutes . ':' . str_pad($time_seconds, 2, '0', STR_PAD_LEFT);
                        } else {
                        echo '0:00';
                        }
                        ?>
                    </div>
                </div>
            </div>

        <div class="message-box <?php echo $attempt['passed'] ? 'pass' : 'fail'; ?>">
            <h3><?php echo $attempt['passed'] ? 'Keep practicing!' : 'Keep practicing!'; ?></h3>
            <p>You answered <?php echo $attempt['score']; ?> out of <?php echo $attempt['total_questions']; ?> questions correctly.</p>
        </div>

        <div class="detailed-results">
            <h3>Detailed Results:</h3>
            
            <?php foreach ($answers as $index => $answer): ?>
            <div class="question-result <?php echo $answer['is_correct'] ? 'correct' : 'incorrect'; ?>">
                <div class="question-result-header">
                    <span class="result-icon <?php echo $answer['is_correct'] ? 'correct' : 'incorrect'; ?>">
                        <?php echo $answer['is_correct'] ? '✓' : '✗'; ?>
                    </span>
                    <div class="question-text">Q<?php echo $index + 1; ?>: <?php echo htmlspecialchars($answer['question']); ?></div>
                </div>
                <div class="answer-info">
                    <div class="your-answer">Your answer: <?php echo htmlspecialchars($answer['option_text']); ?></div>
                    <?php if (!$answer['is_correct']): ?>
                    <div class="correct-answer">Correct answer: <?php echo htmlspecialchars($answer['correct_answer']); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="action-buttons">
            <a href="quiz.php?course_id=<?php echo $attempt['course_id']; ?>" class="btn btn-retake">
                <i class="fas fa-redo"></i> Retake Quiz
            </a>
            <a href="lessons.php?course_id=<?php echo $attempt['course_id']; ?>" class="btn btn-close">
                Close
            </a>
        </div>
    </div>
</body>
</html>