<!-- admim add course.php  -->

<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];

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

// Handle Add Course
if (isset($_POST['add_course'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $status = $_POST['status'];
    
    // Handle image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/courses/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $image_name;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO courses (title, description, image, type, status, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $image, $type, $status, $admin_id]);
    
    $success_msg = "Course added successfully!";
}

// Handle Update Course
if (isset($_POST['update_course'])) {
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $status = $_POST['status'];
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/courses/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $stmt = $pdo->prepare("UPDATE courses SET title=?, description=?, image=?, type=?, status=? WHERE course_id=?");
            $stmt->execute([$title, $description, $image_name, $type, $status, $course_id]);
        }
    } else {
        $stmt = $pdo->prepare("UPDATE courses SET title=?, description=?, type=?, status=? WHERE course_id=?");
        $stmt->execute([$title, $description, $type, $status, $course_id]);
    }
    
    $success_msg = "Course updated successfully!";
}

// Handle Delete Course
if (isset($_GET['delete'])) {
    $course_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM courses WHERE course_id=?");
    $stmt->execute([$course_id]);
    
    $success_msg = "Course deleted successfully!";
}

// Get all courses
$stmt = $pdo->query("SELECT c.*, a.name as admin_name FROM courses c LEFT JOIN admin a ON c.created_by = a.admin_id ORDER BY c.created_at DESC");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management - CodeLearn Admin</title>
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
            max-width: 1400px;
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
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #1E293B 0%, #3B82F6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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

        .btn-success {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-warning {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.3);
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
            .btn-success {
                background: linear-gradient(135deg, #10B981 0%, #059669 100%);
                color: white;
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            }

            .btn-success:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
            }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        select.form-control {
            cursor: pointer;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
            margin-top: 30px;
        }

        .course-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
            transition: all 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
        }

        .course-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }

        .course-content {
            padding: 24px;
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 12px;
        }

        .course-title {
            font-size: 20px;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 8px;
        }

        .course-badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-pro {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            color: white;
        }

        .badge-free {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
        }

        .badge-published {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
        }

        .badge-draft {
            background: rgba(107, 114, 128, 0.12);
            color: #6B7280;
        }

        .course-description {
            color: #6B7280;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .course-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            color: #9CA3AF;
            font-size: 13px;
            margin-bottom: 16px;
            padding-top: 16px;
            border-top: 1px solid #F3F4F6;
        }

        .course-actions {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
        }

        .empty-state {
            text-align: center;
            padding: 80px 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
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
            color: #9CA3AF;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .courses-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Course Management</h1>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Add New Course
            </button>
        </div>

        <?php if (isset($success_msg)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
        </div>
        <?php endif; ?>

        <?php if (count($courses) > 0): ?>
        <div class="courses-grid">
            <?php foreach ($courses as $course): ?>
            <div class="course-card">
                <?php if ($course['image']): ?>
                <img src="uploads/courses/<?php echo htmlspecialchars($course['image']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" class="course-image">
                <?php else: ?>
                <div class="course-image">
                    <i class="fas fa-book"></i>
                </div>
                <?php endif; ?>
                
                <div class="course-content">
                    <div class="course-header">
                        <div>
                            <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                            <div class="course-badges">
                                <span class="badge <?php echo $course['type'] === 'pro' ? 'badge-pro' : 'badge-free'; ?>">
                                    <?php echo $course['type'] === 'pro' ? 'PRO' : 'FREE'; ?>
                                </span>
                                <span class="badge <?php echo $course['status'] === 'published' ? 'badge-published' : 'badge-draft'; ?>">
                                    <?php echo strtoupper($course['status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <p class="course-description">
                        <?php echo htmlspecialchars(substr($course['description'], 0, 120)) . (strlen($course['description']) > 120 ? '...' : ''); ?>
                    </p>
                    
                    <div class="course-meta">
                        <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($course['admin_name']); ?></span>
                        <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($course['created_at'])); ?></span>
                    </div>
                    
                    <div class="course-actions">
                        <a href="admin_lessons.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-book-open"></i> Lessons
                        </a>
                        <button class="btn btn-warning btn-sm" onclick='openEditModal(<?php echo json_encode($course); ?>)'>
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteCourse(<?php echo $course['course_id']; ?>, '<?php echo htmlspecialchars($course['title']); ?>')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">📚</div>
            <h3>No Courses Yet</h3>
            <p>Start by creating your first course</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Add Course Modal -->
    <div class="modal" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Course</h2>
                <button class="close-btn" onclick="closeAddModal()">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Course Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Course Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label>Course Type</label>
                    <select name="type" class="form-control" required>
                        <option value="free">Free</option>
                        <option value="pro">Pro</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                
                <button type="submit" name="add_course" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-plus"></i> Add Course
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Course</h2>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="course_id" id="edit_course_id">
                
                <div class="form-group">
                    <label>Course Title</label>
                    <input type="text" name="title" id="edit_title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_description" class="form-control" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Course Image (leave empty to keep current)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label>Course Type</label>
                    <select name="type" id="edit_type" class="form-control" required>
                        <option value="free">Free</option>
                        <option value="pro">Pro</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="edit_status" class="form-control" required>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                
                <button type="submit" name="update_course" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-save"></i> Update Course
                </button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.add('active');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.remove('active');
        }

        function openEditModal(course) {
            document.getElementById('edit_course_id').value = course.course_id;
            document.getElementById('edit_title').value = course.title;
            document.getElementById('edit_description').value = course.description;
            document.getElementById('edit_type').value = course.type;
            document.getElementById('edit_status').value = course.status;
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        function deleteCourse(courseId, courseName) {
            if (confirm('Are you sure you want to delete "' + courseName + '"? This action cannot be undone.')) {
                window.location.href = '?delete=' + courseId;
            }
        }

        // Close modal on outside click
        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            if (event.target === addModal) {
                closeAddModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>