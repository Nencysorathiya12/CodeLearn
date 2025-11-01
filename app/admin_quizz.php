<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
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

// Get all courses
$stmt = $pdo->query("SELECT * FROM courses ORDER BY title");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle edit mode
$edit_mode = false;
$edit_question = null;
if (isset($_GET['edit']) && isset($_GET['quiz_id'])) {
    $edit_mode = true;
    $quiz_id = (int)$_GET['quiz_id'];
    
    $stmt = $pdo->prepare("
        SELECT q.*, qo.option_id, qo.option_text, qo.is_correct
        FROM quiz q 
        LEFT JOIN quiz_options qo ON q.quiz_id = qo.quiz_id 
        WHERE q.quiz_id = ? 
        ORDER BY qo.option_id
    ");
    $stmt->execute([$quiz_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($results) {
        $edit_question = [
            'quiz_id' => $results[0]['quiz_id'],
            'course_id' => $results[0]['course_id'],
            'question' => $results[0]['question'],
            'options' => []
        ];
        
        foreach ($results as $row) {
            $edit_question['options'][] = [
                'option_id' => $row['option_id'],
                'option_text' => $row['option_text'],
                'is_correct' => $row['is_correct']
            ];
        }
    }
}

// Handle update question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_question'])) {
    $quiz_id = $_POST['quiz_id'];
    $course_id = $_POST['course_id'];
    $question = $_POST['question'];
    $options = $_POST['options'];
    $option_ids = $_POST['option_ids'];
    $correct_option = $_POST['correct_option'];
    
    try {
        $pdo->beginTransaction();
        
        // Update question
        $stmt = $pdo->prepare("UPDATE quiz SET course_id = ?, question = ? WHERE quiz_id = ?");
        $stmt->execute([$course_id, $question, $quiz_id]);
        
        // Update options
        foreach ($options as $index => $option_text) {
            if (!empty($option_text)) {
                $is_correct = ($index == $correct_option) ? 1 : 0;
                $option_id = $option_ids[$index];
                $stmt = $pdo->prepare("UPDATE quiz_options SET option_text = ?, is_correct = ? WHERE option_id = ?");
                $stmt->execute([$option_text, $is_correct, $option_id]);
            }
        }
        
        $pdo->commit();
        $success_message = "Question updated successfully!";
        $edit_mode = false;
        header("Location: ?course_id=" . $course_id);
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Error: " . $e->getMessage();
    }
}

// Handle form submission for adding
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $course_id = $_POST['course_id'];
    $question = $_POST['question'];
    $options = $_POST['options'];
    $correct_option = $_POST['correct_option'];
    
    try {
        $pdo->beginTransaction();
        
        // Insert question
        $stmt = $pdo->prepare("INSERT INTO quiz (course_id, question) VALUES (?, ?)");
        $stmt->execute([$course_id, $question]);
        $quiz_id = $pdo->lastInsertId();
        
        // Insert options
        foreach ($options as $index => $option_text) {
            if (!empty($option_text)) {
                $is_correct = ($index == $correct_option) ? 1 : 0;
                $stmt = $pdo->prepare("INSERT INTO quiz_options (quiz_id, option_text, is_correct) VALUES (?, ?, ?)");
                $stmt->execute([$quiz_id, $option_text, $is_correct]);
            }
        }
        
        $pdo->commit();
        $success_message = "Question added successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Error: " . $e->getMessage();
    }
}

// Handle delete question
if (isset($_GET['delete']) && isset($_GET['quiz_id'])) {
    $quiz_id = (int)$_GET['quiz_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM quiz WHERE quiz_id = ?");
        $stmt->execute([$quiz_id]);
        $success_message = "Question deleted successfully!";
    } catch (Exception $e) {
        $error_message = "Error deleting question: " . $e->getMessage();
    }
}

// Get existing questions
$selected_course = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$existing_questions = [];
if ($selected_course > 0) {
    $stmt = $pdo->prepare("
        SELECT q.*, GROUP_CONCAT(qo.option_text ORDER BY qo.option_id SEPARATOR '|') as options,
               GROUP_CONCAT(qo.is_correct ORDER BY qo.option_id SEPARATOR '|') as correct_flags
        FROM quiz q 
        LEFT JOIN quiz_options qo ON q.quiz_id = qo.quiz_id 
        WHERE q.course_id = ? 
        GROUP BY q.quiz_id 
        ORDER BY q.created_at DESC
    ");
    $stmt->execute([$selected_course]);
    $existing_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Management - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 15px;
            transition: all 0.3s;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .card h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .option-group {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .option-input {
            flex: 1;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .radio-label:hover {
            background: #f0f0f0;
        }

        .radio-label input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
            padding: 8px 16px;
            font-size: 13px;
            margin-right: 5px;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
            padding: 8px 16px;
            font-size: 13px;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            margin-left: 10px;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .questions-list {
            margin-top: 30px;
        }

        .question-item {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 2px solid #f0f0f0;
            transition: all 0.3s;
        }

        .question-item:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .question-text {
            font-weight: 600;
            color: #333;
            flex: 1;
            font-size: 16px;
        }

        .question-actions {
            display: flex;
            gap: 5px;
        }

        .options-list {
            margin-left: 20px;
        }

        .option-item {
            padding: 8px 12px;
            margin: 5px 0;
            border-radius: 6px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .option-item.correct {
            background: #d4edda;
            color: #155724;
            font-weight: 600;
        }

        .option-item.incorrect {
            background: #f8f9fa;
            color: #666;
        }

        .filter-section {
            margin-bottom: 20px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        .edit-mode-indicator {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Quiz Management</h1>
            <p>Create and manage quiz questions for courses</p>
            <a href="admin-panel.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <?php if ($edit_mode && $edit_question): ?>
        <div class="edit-mode-indicator">
            <i class="fas fa-edit" style="font-size: 20px;"></i>
            <strong>Edit Mode:</strong> You are currently editing a question. Update the fields below and click "Update Question".
        </div>
        <?php endif; ?>

        <!-- Add/Edit Question Form -->
        <div class="card">
            <h2>
                <i class="fas fa-<?php echo $edit_mode ? 'edit' : 'plus-circle'; ?>"></i> 
                <?php echo $edit_mode ? 'Edit Question' : 'Add New Question'; ?>
            </h2>
            <form method="POST">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="quiz_id" value="<?php echo $edit_question['quiz_id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label class="form-label">Select Course *</label>
                    <select name="course_id" class="form-control" required>
                        <option value="">Choose a course...</option>
                        <?php 
                        if (count($courses) > 0) {
                            foreach ($courses as $course): 
                                $selected = '';
                                if ($edit_mode && $edit_question['course_id'] == $course['course_id']) {
                                    $selected = 'selected';
                                } elseif (!$edit_mode && $selected_course == $course['course_id']) {
                                    $selected = 'selected';
                                }
                            ?>
                                <option value="<?php echo $course['course_id']; ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </option>
                            <?php endforeach;
                        } else {
                            echo '<option value="">No courses available - Add courses first!</option>';
                        }
                        ?>
                    </select>
                    <small style="color: #666; margin-top: 5px; display: block;">
                        Total courses available: <?php echo count($courses); ?>
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Question *</label>
                    <textarea name="question" class="form-control" rows="3" placeholder="Enter your question here..." required><?php echo $edit_mode ? htmlspecialchars($edit_question['question']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Options (Select the correct answer) *</label>
                    
                    <?php for ($i = 0; $i < 4; $i++): 
                        $option_text = '';
                        $is_checked = false;
                        $option_id = '';
                        
                        if ($edit_mode && isset($edit_question['options'][$i])) {
                            $option_text = $edit_question['options'][$i]['option_text'];
                            $is_checked = $edit_question['options'][$i]['is_correct'] == 1;
                            $option_id = $edit_question['options'][$i]['option_id'];
                        } else {
                            $is_checked = ($i === 0);
                        }
                    ?>
                    <div class="option-group">
                        <?php if ($edit_mode): ?>
                            <input type="hidden" name="option_ids[]" value="<?php echo $option_id; ?>">
                        <?php endif; ?>
                        <input type="text" name="options[]" class="form-control option-input" 
                               placeholder="Option <?php echo $i + 1; ?>" 
                               value="<?php echo htmlspecialchars($option_text); ?>" required>
                        <label class="radio-label">
                            <input type="radio" name="correct_option" value="<?php echo $i; ?>" <?php echo $is_checked ? 'checked' : ''; ?> required>
                            <span>Correct</span>
                        </label>
                    </div>
                    <?php endfor; ?>
                </div>

                <?php if ($edit_mode): ?>
                    <button type="submit" name="update_question" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Question
                    </button>
                    <a href="?course_id=<?php echo $edit_question['course_id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                <?php else: ?>
                    <button type="submit" name="add_question" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Question
                    </button>
                <?php endif; ?>
            </form>
        </div>

        <!-- Filter Section -->
        <div class="card filter-section">
            <h2><i class="fas fa-filter"></i> View Questions by Course</h2>
            <form method="GET">
                <div class="form-group">
                    <select name="course_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Select a course to view questions...</option>
                        <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['course_id']; ?>" <?php echo ($selected_course == $course['course_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['title']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>

        <!-- Statistics -->
        <?php if ($selected_course > 0): ?>
        <div class="stats">
            <div class="stat-box">
                <div class="stat-number"><?php echo count($existing_questions); ?></div>
                <div class="stat-label">Total Questions</div>
            </div>
        </div>

        <!-- Existing Questions -->
        <div class="card questions-list">
            <h2><i class="fas fa-list"></i> Existing Questions (<?php echo count($existing_questions); ?>)</h2>
            
            <?php if (count($existing_questions) > 0): ?>
                <?php foreach ($existing_questions as $index => $q): ?>
                <div class="question-item">
                    <div class="question-header">
                        <div class="question-text">Q<?php echo $index + 1; ?>: <?php echo htmlspecialchars($q['question']); ?></div>
                        <div class="question-actions">
                            <a href="?course_id=<?php echo $selected_course; ?>&edit=1&quiz_id=<?php echo $q['quiz_id']; ?>" 
                               class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?course_id=<?php echo $selected_course; ?>&delete=1&quiz_id=<?php echo $q['quiz_id']; ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this question?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                    <div class="options-list">
                        <?php 
                        $options = explode('|', $q['options']);
                        $correct_flags = explode('|', $q['correct_flags']);
                        foreach ($options as $opt_index => $option):
                            $is_correct = isset($correct_flags[$opt_index]) && $correct_flags[$opt_index] == '1';
                        ?>
                        <div class="option-item <?php echo $is_correct ? 'correct' : 'incorrect'; ?>">
                            <?php echo $is_correct ? '<i class="fas fa-check-circle"></i>' : '<i class="far fa-circle"></i>'; ?>
                            <?php echo htmlspecialchars($option); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 40px;">No questions added yet for this course. Add your first question above!</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>