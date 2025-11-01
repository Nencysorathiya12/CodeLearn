<?php
session_start();

if (!isset($_SESSION['user_id'])) {
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

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Get course details
$stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Course not found!");
}

// Get quiz questions for this course
// Get quiz questions for this course (show all available questions)
$stmt = $pdo->prepare("SELECT * FROM quiz WHERE course_id = ? ORDER BY RAND()");
$stmt->execute([$course_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if enough questions exist
if (count($questions) == 0) {
    die("No quiz questions available for this course!");
}

// Use all available questions (or limit to 15 if more than 15 exist)
if (count($questions) > 15) {
    $questions = array_slice($questions, 0, 15);
}

// Get options for each question
foreach ($questions as &$question) {
    $stmt = $pdo->prepare("SELECT * FROM quiz_options WHERE quiz_id = ?");
    $stmt->execute([$question['quiz_id']]);
    $question['options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Create quiz attempt with proper start_time
$user_id = $_SESSION['user_id'];
$total_questions = count($questions);
$duration_minutes = 10;
$start_time = date('Y-m-d H:i:s'); // Current timestamp

$stmt = $pdo->prepare("INSERT INTO user_quiz_attempts (user_id, course_id, total_questions, duration_minutes, start_time) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$user_id, $course_id, $total_questions, $duration_minutes, $start_time]);
$attempt_id = $pdo->lastInsertId();

$_SESSION['quiz_attempt_id'] = $attempt_id;
$_SESSION['quiz_start_time'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> Quiz</title>
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

        .quiz-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .quiz-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            padding: 30px;
            color: white;
            text-align: center;
        }

        .quiz-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .quiz-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .timer {
            font-size: 24px;
            font-weight: 700;
            color: #FFD700;
        }

        .quiz-content {
            padding: 40px;
        }

        .question-card {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .question-card.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .question-number {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .question-text {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .option {
            padding: 18px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .option:hover {
            border-color: #667eea;
            background: #f8f9ff;
            transform: translateX(5px);
        }

        .option.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        }

        .option-radio {
            width: 20px;
            height: 20px;
            border: 2px solid #ccc;
            border-radius: 50%;
            position: relative;
            flex-shrink: 0;
        }

        .option.selected .option-radio {
            border-color: #667eea;
        }

        .option.selected .option-radio::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            background: #667eea;
            border-radius: 50%;
        }

        .option-text {
            flex: 1;
            font-size: 16px;
            color: #333;
        }

        .quiz-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #f0f0f0;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 15px;
        }

        .btn-prev {
            background: #6c757d;
            color: white;
        }

        .btn-prev:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-next {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-submit {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .progress-bar {
            height: 6px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            transition: width 0.3s ease;
        }

        @media (max-width: 768px) {
            .quiz-content {
                padding: 25px;
            }

            .quiz-title {
                font-size: 22px;
            }

            .question-text {
                font-size: 18px;
            }

            .quiz-info {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <div class="quiz-header">
            <div class="quiz-title"><?php echo htmlspecialchars($course['title']); ?> Quiz</div>
            <div class="quiz-info">
                <div class="info-item">
                    <i class="fas fa-question-circle"></i>
                    <span><?php echo $total_questions; ?> Questions</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span class="timer" id="timer">10:00</span>
                </div>
            </div>
            <div class="progress-bar" style="margin-top: 20px;">
                <div class="progress-fill" id="progressBar" style="width: 0%;"></div>
            </div>
        </div>

        <form id="quizForm" method="POST" action="submit_quiz.php">
            <input type="hidden" name="attempt_id" value="<?php echo $attempt_id; ?>">
            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
            
            <div class="quiz-content">
                <?php foreach ($questions as $index => $question): ?>
                <div class="question-card <?php echo $index === 0 ? 'active' : ''; ?>" data-question="<?php echo $index; ?>">
                    <div class="question-number">Question <?php echo $index + 1; ?> of <?php echo $total_questions; ?></div>
                    <div class="question-text"><?php echo htmlspecialchars($question['question']); ?></div>
                    
                    <div class="options">
                        <?php foreach ($question['options'] as $option): ?>
                        <label class="option">
                            <div class="option-radio"></div>
                            <span class="option-text"><?php echo htmlspecialchars($option['option_text']); ?></span>
                            <input type="radio" 
                                   name="question_<?php echo $question['quiz_id']; ?>" 
                                   value="<?php echo $option['option_id']; ?>" 
                                   style="display: none;"
                                   data-quiz-id="<?php echo $question['quiz_id']; ?>">
                        </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="quiz-navigation">
                        <button type="button" class="btn btn-prev" onclick="previousQuestion()" <?php echo $index === 0 ? 'style="visibility: hidden;"' : ''; ?>>
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                        
                        <?php if ($index < $total_questions - 1): ?>
                        <button type="button" class="btn btn-next" onclick="nextQuestion()">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                        <?php else: ?>
                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-check"></i> Submit Quiz
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </form>
    </div>

    <script>
        let currentQuestion = 0;
        const totalQuestions = <?php echo $total_questions; ?>;
        const duration = <?php echo $duration_minutes * 60; ?>; // in seconds
        let timeLeft = duration;

        // Timer
        const timerElement = document.getElementById('timer');
        const timerInterval = setInterval(() => {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 60) {
                timerElement.style.color = '#ff4444';
            }
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                document.getElementById('quizForm').submit();
            }
        }, 1000);

        // Option selection
        document.querySelectorAll('.option').forEach(option => {
            option.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                const questionCard = this.closest('.question-card');
                
                questionCard.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                radio.checked = true;
                
                updateProgress();
            });
        });

        function nextQuestion() {
            if (currentQuestion < totalQuestions - 1) {
                document.querySelectorAll('.question-card')[currentQuestion].classList.remove('active');
                currentQuestion++;
                document.querySelectorAll('.question-card')[currentQuestion].classList.add('active');
                updateProgress();
            }
        }

        function previousQuestion() {
            if (currentQuestion > 0) {
                document.querySelectorAll('.question-card')[currentQuestion].classList.remove('active');
                currentQuestion--;
                document.querySelectorAll('.question-card')[currentQuestion].classList.add('active');
                updateProgress();
            }
        }

        function updateProgress() {
            const answeredCount = document.querySelectorAll('input[type="radio"]:checked').length;
            const progress = (answeredCount / totalQuestions) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
        }

        // Form submission
        // Form submission
document.getElementById('quizForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const answeredCount = document.querySelectorAll('input[type="radio"]:checked').length;
    if (answeredCount < totalQuestions) {
        // Show custom modal instead of alert
        showConfirmModal(answeredCount);
    } else {
        clearInterval(timerInterval);
        this.submit();
    }
});

function showConfirmModal(answeredCount) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); display: flex; align-items: center;
        justify-content: center; z-index: 10000; backdrop-filter: blur(5px);
    `;
    
    modal.innerHTML = `
        <div style="background: white; padding: 40px; border-radius: 20px; max-width: 450px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <div style="font-size: 60px; margin-bottom: 20px;">⚠️</div>
            <h2 style="margin-bottom: 15px; color: #333;">Incomplete Quiz</h2>
            <p style="color: #666; margin-bottom: 30px; line-height: 1.6;">
                You have answered <strong>${answeredCount}</strong> out of <strong>${totalQuestions}</strong> questions.
                <br>Do you want to submit anyway?
            </p>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button onclick="this.closest('div').parentElement.parentElement.remove()" 
                        style="padding: 12px 30px; border: 2px solid #ddd; background: white; 
                               color: #666; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    Go Back
                </button>
                <button onclick="submitQuizNow()" 
                        style="padding: 12px 30px; border: none; background: linear-gradient(135deg, #667eea, #764ba2); 
                               color: white; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 15px;">
                    Submit Quiz
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

function submitQuizNow() {
    clearInterval(timerInterval);
    document.getElementById('quizForm').submit();
}

// Prevent page refresh
window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = '';
});

    </script>
</body>
</html>