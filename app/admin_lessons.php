<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Database configuration
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

// Get course_id from URL
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Get course details
$stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Course not found!");
}

$success_msg = '';
$error_msg = '';

// Handle Add Lesson
if (isset($_POST['add_lesson'])) {
    $lesson_title = $_POST['lesson_title'];
    
    // Handle lesson file upload
    $lesson_file = null;
    if (isset($_FILES['lesson_file']) && $_FILES['lesson_file']['error'] == 0) {
        $target_dir = "uploads/lessons/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['lesson_file']['name'], PATHINFO_EXTENSION);
        if ($file_extension === 'html') {
            $file_name = time() . '_' . basename($_FILES['lesson_file']['name']);
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES['lesson_file']['tmp_name'], $target_file)) {
                $lesson_file = $file_name;
            }
        } else {
            $error_msg = "Only HTML files are allowed!";
        }
    }
    
    if ($lesson_file) {
        $stmt = $pdo->prepare("INSERT INTO lessons (course_id, lesson_title, lesson_file) VALUES (?, ?, ?)");
        $stmt->execute([$course_id, $lesson_title, $lesson_file]);
        $success_msg = "Lesson added successfully!";
    }
}

// Handle Edit Lesson
if (isset($_POST['edit_lesson'])) {
    $lesson_id = $_POST['lesson_id'];
    $lesson_title = $_POST['lesson_title'];
    
    // Check if new file is uploaded
    if (isset($_FILES['lesson_file']) && $_FILES['lesson_file']['error'] == 0) {
        $target_dir = "uploads/lessons/";
        $file_extension = pathinfo($_FILES['lesson_file']['name'], PATHINFO_EXTENSION);
        
        if ($file_extension === 'html') {
            // Get old file to delete
            $stmt = $pdo->prepare("SELECT lesson_file FROM lessons WHERE lesson_id = ?");
            $stmt->execute([$lesson_id]);
            $old_lesson = $stmt->fetch();
            
            if ($old_lesson && $old_lesson['lesson_file']) {
                $old_file_path = $target_dir . $old_lesson['lesson_file'];
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
            }
            
            // Upload new file
            $file_name = time() . '_' . basename($_FILES['lesson_file']['name']);
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES['lesson_file']['tmp_name'], $target_file)) {
                $stmt = $pdo->prepare("UPDATE lessons SET lesson_title = ?, lesson_file = ? WHERE lesson_id = ?");
                $stmt->execute([$lesson_title, $file_name, $lesson_id]);
                $success_msg = "Lesson updated successfully!";
            }
        } else {
            $error_msg = "Only HTML files are allowed!";
        }
    } else {
        // Only update title
        $stmt = $pdo->prepare("UPDATE lessons SET lesson_title = ? WHERE lesson_id = ?");
        $stmt->execute([$lesson_title, $lesson_id]);
        $success_msg = "Lesson updated successfully!";
    }
}

// Handle Delete Lesson
if (isset($_GET['delete_lesson'])) {
    $lesson_id = $_GET['delete_lesson'];
    
    // Get lesson file to delete
    $stmt = $pdo->prepare("SELECT lesson_file FROM lessons WHERE lesson_id = ?");
    $stmt->execute([$lesson_id]);
    $lesson = $stmt->fetch();
    
    if ($lesson && $lesson['lesson_file']) {
        $file_path = "uploads/lessons/" . $lesson['lesson_file'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM lessons WHERE lesson_id = ?");
    $stmt->execute([$lesson_id]);
    
    $success_msg = "Lesson deleted successfully!";
}

// Get all lessons for this course
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY created_at ASC");
$stmt->execute([$course_id]);
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lessons - <?php echo htmlspecialchars($course['title']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #F8F9FA;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 28px 36px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1F2937;
        }

        .course-badge {
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-back {
            background: #6B7280;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 32px;
            border-radius: 20px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #F3F4F6;
        }

        .modal-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1F2937;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #9CA3AF;
            transition: all 0.3s ease;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .close-btn:hover {
            background: #F3F4F6;
            color: #1F2937;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .lessons-table {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
        }

        .table-header {
            padding: 24px 28px;
            border-bottom: 1px solid #F3F4F6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #FAFBFC;
        }

        .table-title {
            font-size: 20px;
            font-weight: 700;
            color: #1F2937;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 18px 20px;
            text-align: left;
            border-bottom: 1px solid #F3F4F6;
        }

        th {
            background: #FAFBFC;
            color: #6B7280;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            color: #4B5563;
            font-size: 14px;
        }

        tr:hover {
            background: #FAFBFC;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .empty-state {
            text-align: center;
            padding: 80px 40px;
            color: #9CA3AF;
        }

        .empty-state-icon {
            font-size: 72px;
            margin-bottom: 20px;
            opacity: 0.6;
        }

        .empty-state h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #6B7280;
            font-weight: 700;
        }

        .empty-state p {
            font-size: 15px;
            opacity: 0.8;
        }

        .lesson-actions {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .file-info {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #F3F4F6;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 13px;
            color: #6B7280;
        }

        /* Delete Confirmation Popup */
        .delete-popup {
            background: white;
            padding: 32px;
            border-radius: 20px;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .delete-popup-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: #EF4444;
        }

        .delete-popup h3 {
            font-size: 22px;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 12px;
        }

        .delete-popup p {
            color: #6B7280;
            font-size: 15px;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .delete-popup-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn-cancel {
            background: #E5E7EB;
            color: #374151;
        }

        .btn-cancel:hover {
            background: #D1D5DB;
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            display: none;
            align-items: center;
            gap: 12px;
            z-index: 2000;
            min-width: 300px;
            animation: slideIn 0.3s ease;
        }

        .toast.show {
            display: flex;
        }

        .toast-success {
            border-left: 4px solid #10B981;
        }

        .toast-error {
            border-left: 4px solid #EF4444;
        }

        .toast-icon {
            font-size: 24px;
        }

        .toast-success .toast-icon {
            color: #10B981;
        }

        .toast-error .toast-icon {
            color: #EF4444;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 4px;
        }

        .toast-message {
            font-size: 14px;
            color: #6B7280;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Manage Lessons</h1>
                <div class="course-badge"><?php echo htmlspecialchars($course['title']); ?></div>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="admin_courses.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Courses</a>
                <button class="btn btn-primary" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add Lesson
                </button>
            </div>
        </div>

        <div class="lessons-table">
            <div class="table-header">
                <h2 class="table-title">Lessons (<?php echo count($lessons); ?>)</h2>
            </div>

            <?php if (count($lessons) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lesson Title</th>
                        <th>File</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lessons as $index => $lesson): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td style="font-weight: 600;"><?php echo htmlspecialchars($lesson['lesson_title']); ?></td>
                        <td>
                            <span class="file-info">
                                <i class="fas fa-file-code"></i>
                                <?php echo htmlspecialchars($lesson['lesson_file']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($lesson['created_at'])); ?></td>
                        <td>
                            <div class="lesson-actions">
                                <a href="uploads/lessons/<?php echo $lesson['lesson_file']; ?>" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <button class="btn btn-success btn-sm" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($lesson)); ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="openDeletePopup(<?php echo $lesson['lesson_id']; ?>, '<?php echo htmlspecialchars($lesson['lesson_title'], ENT_QUOTES); ?>')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📖</div>
                <h3>No Lessons Yet</h3>
                <p>Start by adding your first lesson to this course</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Lesson Modal -->
    <div class="modal" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Lesson</h2>
                <button class="close-btn" onclick="closeAddModal()">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Lesson Title</label>
                    <input type="text" name="lesson_title" class="form-control" placeholder="e.g., JavaScript Introduction" required>
                </div>
                
                <div class="form-group">
                    <label>Lesson File</label>
                    <input type="file" name="lesson_file" class="form-control" accept=".html" required>
                    <small style="color: #6B7280; font-size: 12px;"></small>
                </div>
                
                <button type="submit" name="add_lesson" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-plus"></i> Add Lesson
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Lesson Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Lesson</h2>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="lesson_id" id="edit_lesson_id">
                
                <div class="form-group">
                    <label>Lesson Title</label>
                    <input type="text" name="lesson_title" id="edit_lesson_title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Current File</label>
                    <div class="file-info" id="current_file" style="width: 100%; justify-content: flex-start;">
                        <i class="fas fa-file-code"></i>
                        <span id="current_filename"></span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>New Lesson File (Optional)</label>
                    <input type="file" name="lesson_file" class="form-control" accept=".html">
                    <small style="color: #6B7280; font-size: 12px;">Leave empty to keep current file</small>
                </div>
                
                <button type="submit" name="edit_lesson" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-save"></i> Update Lesson
                </button>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Popup -->
    <div class="modal" id="deleteModal">
        <div class="delete-popup">
            <div class="delete-popup-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Delete Lesson?</h3>
            <p id="delete-message">Are you sure you want to delete this lesson? This action cannot be undone.</p>
            <div class="delete-popup-actions">
                <button class="btn btn-cancel" onclick="closeDeletePopup()">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast <?php echo !empty($success_msg) ? 'toast-success' : (!empty($error_msg) ? 'toast-error' : ''); ?>" id="toast">
        <div class="toast-icon">
            <i class="fas <?php echo !empty($success_msg) ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title"><?php echo !empty($success_msg) ? 'Success' : 'Error'; ?></div>
            <div class="toast-message"><?php echo !empty($success_msg) ? $success_msg : $error_msg; ?></div>
        </div>
    </div>

    <script>
        // Show toast if there's a message
        <?php if (!empty($success_msg) || !empty($error_msg)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            showToast();
        });
        <?php endif; ?>

        function showToast() {
            const toast = document.getElementById('toast');
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        function openAddModal() {
            document.getElementById('addModal').classList.add('active');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.remove('active');
        }

        function openEditModal(lesson) {
            document.getElementById('edit_lesson_id').value = lesson.lesson_id;
            document.getElementById('edit_lesson_title').value = lesson.lesson_title;
            document.getElementById('current_filename').textContent = lesson.lesson_file;
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        function openDeletePopup(lessonId, lessonTitle) {
            document.getElementById('delete-message').innerHTML = 
                `Are you sure you want to delete "<strong>${lessonTitle}</strong>"? This action cannot be undone.`;
            
            document.getElementById('confirmDeleteBtn').onclick = function() {
                window.location.href = '?course_id=<?php echo $course_id; ?>&delete_lesson=' + lessonId;
            };
            
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeletePopup() {
            document.getElementById('deleteModal').classList.remove('active');
        }

        // Close modals on outside click
        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (event.target === addModal) {
                closeAddModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
            if (event.target === deleteModal) {
                closeDeletePopup();
            }
        }
    </script>
</body>
</html>