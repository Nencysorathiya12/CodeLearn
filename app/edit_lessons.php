<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

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

$lesson_id = isset($_GET['lesson_id']) ? (int)$_GET['lesson_id'] : 0;
$success_message = '';
$error_message = '';

// Get lesson details
$stmt = $pdo->prepare("SELECT l.*, c.title as course_title, c.course_id 
                       FROM lessons l 
                       JOIN courses c ON l.course_id = c.course_id 
                       WHERE l.lesson_id = ?");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lesson) {
    die("Lesson not found!");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lesson_title = trim($_POST['lesson_title']);
    
    // Handle file upload if new file is provided
    if (isset($_FILES['lesson_file']) && $_FILES['lesson_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['lesson_file'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Validate file type
        if ($file_ext !== 'html') {
            $error_message = "Only HTML files are allowed!";
        } else {
            // Delete old file
            $old_file_path = 'uploads/lessons/' . $lesson['lesson_file'];
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }
            
            // Generate unique filename
            $new_file_name = uniqid() . '_' . $file_name;
            $upload_path = 'uploads/lessons/' . $new_file_name;
            
            // Create directory if it doesn't exist
            if (!is_dir('uploads/lessons')) {
                mkdir('uploads/lessons', 0777, true);
            }
            
            // Move uploaded file
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Update database with new file
                $stmt = $pdo->prepare("UPDATE lessons SET lesson_title = ?, lesson_file = ? WHERE lesson_id = ?");
                $stmt->execute([$lesson_title, $new_file_name, $lesson_id]);
                
                $success_message = "Lesson updated successfully with new file!";
                
                // Refresh lesson data
                $stmt = $pdo->prepare("SELECT l.*, c.title as course_title, c.course_id 
                                       FROM lessons l 
                                       JOIN courses c ON l.course_id = c.course_id 
                                       WHERE l.lesson_id = ?");
                $stmt->execute([$lesson_id]);
                $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error_message = "Failed to upload file!";
            }
        }
    } else {
        // Update only lesson title if no new file
        $stmt = $pdo->prepare("UPDATE lessons SET lesson_title = ? WHERE lesson_id = ?");
        $stmt->execute([$lesson_title, $lesson_id]);
        
        $success_message = "Lesson title updated successfully!";
        $lesson['lesson_title'] = $lesson_title;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lesson - <?php echo htmlspecialchars($lesson['lesson_title']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .header h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2rem;
        }

        .breadcrumb {
            color: #666;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .form-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .form-group input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .file-upload-area {
            border: 2px dashed #e1e8ed;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .file-upload-area:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .file-upload-area.active {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .file-upload-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 15px;
        }

        .file-upload-text {
            color: #666;
            margin-bottom: 10px;
        }

        .file-upload-text strong {
            color: #333;
        }

        .file-info {
            margin-top: 15px;
            padding: 12px;
            background: #e8f5e9;
            border-radius: 6px;
            color: #2e7d32;
            font-weight: 500;
        }

        .current-file {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .current-file strong {
            color: #856404;
        }

        input[type="file"] {
            display: none;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 14px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .preview-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e1e8ed;
        }

        .preview-section h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .preview-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #f8f9fa;
            border: 1px solid #e1e8ed;
            border-radius: 6px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .preview-link:hover {
            background: #667eea;
            color: white;
        }

        /* Upgrade Modal */
        .upgrade-modal {
            display: none;
            position: fixed;
            z-index: 3000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .upgrade-modal-content {
            background: white;
            margin: 10% auto;
            border-radius: 24px;
            width: 90%;
            max-width: 500px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            animation: slideUp 0.4s ease;
        }

        @keyframes slideUp {
            from { 
                opacity: 0; 
                transform: translateY(50px) scale(0.9); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1); 
            }
        }

        .upgrade-header {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
            position: relative;
        }

        .upgrade-icon {
            font-size: 64px;
            margin-bottom: 15px;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .upgrade-close {
            position: absolute;
            right: 20px;
            top: 20px;
            color: white;
            font-size: 28px;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            background: rgba(255,255,255,0.2);
        }

        .upgrade-close:hover {
            background: rgba(255,255,255,0.3);
            transform: rotate(90deg);
        }

        .upgrade-header h2 {
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: 800;
        }

        .upgrade-header p {
            font-size: 16px;
            opacity: 0.95;
        }

        .upgrade-body {
            padding: 40px 35px;
            text-align: center;
        }

        .upgrade-message {
            font-size: 18px;
            color: #333;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .upgrade-features {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: left;
        }

        .upgrade-features h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
            text-align: center;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            color: #555;
        }

        .feature-item i {
            color: #FFA500;
            font-size: 18px;
            min-width: 24px;
        }

        .upgrade-buttons {
            display: flex;
            gap: 12px;
        }

        .upgrade-btn {
            flex: 1;
            padding: 16px 24px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .upgrade-btn-primary {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 165, 0, 0.4);
        }

        .upgrade-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 165, 0, 0.5);
        }

        .upgrade-btn-secondary {
            background: #f1f3f5;
            color: #495057;
        }

        .upgrade-btn-secondary:hover {
            background: #e9ecef;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px 10px;
            }

            .header, .form-card {
                padding: 20px;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .upgrade-modal-content {
                width: 95%;
                margin: 20% auto;
            }

            .upgrade-header {
                padding: 30px 20px;
            }

            .upgrade-header h2 {
                font-size: 24px;
            }

            .upgrade-body {
                padding: 30px 20px;
            }

            .upgrade-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-edit"></i> Edit Lesson</h1>
            <div class="breadcrumb">
                <a href="admin_dashboard.php">Dashboard</a> / 
                <a href="manage_lessons.php?course_id=<?php echo $lesson['course_id']; ?>">
                    <?php echo htmlspecialchars($lesson['course_title']); ?>
                </a> / 
                Edit Lesson
            </div>
        </div>

        <div class="form-card">
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="lesson_title">
                        <i class="fas fa-heading"></i> Lesson Title
                    </label>
                    <input 
                        type="text" 
                        id="lesson_title" 
                        name="lesson_title" 
                        value="<?php echo htmlspecialchars($lesson['lesson_title']); ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-file-upload"></i> Replace Lesson File (Optional)
                    </label>
                    
                    <div class="current-file">
                        <strong>Current File:</strong> <?php echo htmlspecialchars($lesson['lesson_file']); ?>
                    </div>

                    <div class="file-upload-area" id="fileUploadArea" onclick="document.getElementById('lesson_file').click()">
                        <div class="file-upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="file-upload-text">
                            <strong>Click to upload</strong> or drag and drop
                        </div>
                        <div class="file-upload-text">
                            HTML files only
                        </div>
                        <div id="fileInfo" class="file-info" style="display: none;"></div>
                    </div>
                    
                    <input 
                        type="file" 
                        id="lesson_file" 
                        name="lesson_file" 
                        accept=".html"
                    >
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Lesson
                    </button>
                    <a href="manage_lessons.php?course_id=<?php echo $lesson['course_id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>

            <div class="preview-section">
                <h3><i class="fas fa-eye"></i> Preview Lesson</h3>
                <a href="lessons.php?course_id=<?php echo $lesson['course_id']; ?>" class="preview-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    View in Learning Platform
                </a>
            </div>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('lesson_file');
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInfo = document.getElementById('fileInfo');

        // File input change event
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                fileInfo.textContent = `Selected: ${file.name} (${(file.size / 1024).toFixed(2)} KB)`;
                fileInfo.style.display = 'block';
                fileUploadArea.classList.add('active');
            }
        });

        // Drag and drop functionality
        fileUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('active');
        });

        fileUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('active');
        });

        fileUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('active');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        });
    </script>
</body>
</html>