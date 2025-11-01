<?php
session_start();
require_once './config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['name'];
$userPlan = isset($_SESSION['plan']) ? $_SESSION['plan'] : 'free';

// Check if user is Pro or Max
if ($userPlan !== 'pro' && $userPlan !== 'team') {
    header('Location: pricing.php');
    exit();
}

// Get all available courses
$stmt = $conn->prepare("SELECT course_id, title FROM courses ORDER BY title ASC");
$stmt->execute();
$courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get user's existing certificates
$stmt = $conn->prepare("SELECT course_id FROM certificates WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$existingCerts = [];
while ($row = $result->fetch_assoc()) {
    $existingCerts[] = $row['course_id'];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Certificate - CodeLearn</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 30px;
            transition: all 0.3s;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateX(-5px);
        }

        .generator-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-label i {
            margin-right: 8px;
            color: #667eea;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            outline: none;
            background: #fafafa;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-select {
            cursor: pointer;
        }

        .course-option.disabled {
            color: #999;
        }

        .generate-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .generate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .generate-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .message {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.show {
            display: block;
        }

        .info-box {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
            color: #856404;
            font-size: 14px;
        }

        .info-box i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
            }

            .generator-card {
                padding: 25px;
            }

            .header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="certificates.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Certificates
        </a>

        <div class="generator-card">
            <div class="header">
                <div class="header-icon">🎓</div>
                <h1>Generate Certificate</h1>
                <p>Create your course completion certificate</p>
            </div>

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                You can generate one certificate per course. Already generated certificates cannot be regenerated.
            </div>

            <div id="messageBox" class="message"></div>

            <form id="certificateForm" onsubmit="generateCertificate(event)">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i> Your Name
                    </label>
                    <input type="text" 
                           id="userName" 
                           class="form-input" 
                           value="<?php echo htmlspecialchars($userName); ?>" 
                           required 
                           minlength="2"
                           maxlength="50">
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-book"></i> Select Course
                    </label>
                    <select id="courseId" class="form-select" required>
                        <option value="">-- Choose a course --</option>
                        <?php foreach ($courses as $course): ?>
                            <?php 
                            $isGenerated = in_array($course['course_id'], $existingCerts);
                            ?>
                            <option value="<?php echo $course['course_id']; ?>" 
                                    <?php echo $isGenerated ? 'disabled' : ''; ?>
                                    class="<?php echo $isGenerated ? 'course-option disabled' : 'course-option'; ?>">
                                <?php echo htmlspecialchars($course['title']); ?>
                                <?php echo $isGenerated ? ' (Already Generated)' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="generate-btn" id="submitBtn">
                    <i class="fas fa-certificate"></i> Generate Certificate
                </button>
            </form>
        </div>
    </div>

    <script>
        function showMessage(message, type) {
            const messageBox = document.getElementById('messageBox');
            messageBox.textContent = message;
            messageBox.className = 'message ' + type + ' show';
            
            if (type === 'success') {
                setTimeout(() => {
                    messageBox.classList.remove('show');
                }, 5000);
            }
        }

        async function generateCertificate(event) {
            event.preventDefault();
            
            const userName = document.getElementById('userName').value.trim();
            const courseId = document.getElementById('courseId').value;
            const submitBtn = document.getElementById('submitBtn');
            
            if (!courseId) {
                showMessage('Please select a course', 'error');
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            
            try {
                const formData = new FormData();
                formData.append('action', 'generate');
                formData.append('name', userName);
                formData.append('course_id', courseId);
                
                const response = await fetch('process_certificate.php', {
                    method: 'POST',
                    body: formData
                });
                
                // Check if response is OK
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    
                    setTimeout(() => {
                        window.location.href = 'view_certificate.php?course_id=' + courseId;
                    }, 1500);
                } else {
                    showMessage(result.message, 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-certificate"></i> Generate Certificate';
                }
                
            } catch (error) {
                console.error('Error:', error);
                showMessage('Network error: ' + error.message + '. Please check if process_certificate.php file exists.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-certificate"></i> Generate Certificate';
            }
        }
    </script>
</body>
</html>