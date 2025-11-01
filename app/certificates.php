<?php
session_start();
require_once './config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    // Store intended page for redirect after login
    $_SESSION['redirect_after_login'] = 'certificates.php';
}

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$userName = isset($_SESSION['name']) ? $_SESSION['name'] : '';
$userPlan = isset($_SESSION['plan']) ? $_SESSION['plan'] : 'free';

// Get all certificates for this user if logged in
$completedCourses = [];
if ($userId) {
    $stmt = $conn->prepare("
        SELECT cert.certificate_id, cert.course_id, c.title, cert.issued_date, cert.certificate_number
        FROM certificates cert
        INNER JOIN courses c ON cert.course_id = c.course_id
        WHERE cert.user_id = ?
        ORDER BY cert.issued_date DESC
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $completedCourses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Certificates - CodeLearn</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
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

        .certificates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .cert-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .cert-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .cert-icon {
            font-size: 3rem;
            text-align: center;
            margin-bottom: 20px;
        }

        .cert-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .cert-course {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 15px;
            text-align: center;
        }

        .cert-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .cert-info-item {
            text-align: center;
        }

        .cert-info-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }

        .cert-info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .cert-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 20px;
        }

        .cert-btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-view {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-download {
            background: #28a745;
            color: white;
        }

        .btn-download:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .no-certificates {
            text-align: center;
            background: white;
            padding: 60px 40px;
            border-radius: 15px;
            margin-top: 50px;
        }

        .no-certificates i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        .no-certificates h2 {
            color: #333;
            margin-bottom: 10px;
        }

        .no-certificates p {
            color: #666;
            margin-bottom: 20px;
        }

        .upgrade-notice {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            color: #333;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .upgrade-notice i {
            margin-right: 10px;
        }

        .upgrade-notice a {
            color: #667eea;
            text-decoration: underline;
        }

        /* Auth Modal Styles */
        .auth-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(3px);
        }

        .auth-modal-content {
            background: white;
            margin: 8% auto;
            border-radius: 16px;
            width: 90%;
            max-width: 380px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(-20px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .auth-modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            text-align: center;
            color: white;
            position: relative;
        }

        .modal-close {
            position: absolute;
            right: 15px;
            top: 15px;
            color: white;
            font-size: 20px;
            cursor: pointer;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: rgba(255,255,255,0.2);
        }

        .modal-logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .modal-subtitle {
            font-size: 13px;
            opacity: 0.9;
        }

        .auth-forms {
            padding: 25px;
        }

        .form-tabs {
            display: flex;
            margin-bottom: 20px;
            background: #f5f5f5;
            border-radius: 6px;
            padding: 3px;
        }

        .tab-btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            background: transparent;
            color: #666;
            font-weight: 500;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }

        .tab-btn.active {
            background: white;
            color: #333;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-content {
            display: none;
        }

        .form-content.active {
            display: block;
        }

        .divider {
            text-align: center;
            margin: 15px 0;
            position: relative;
            color: #999;
            font-size: 12px;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #eee;
        }

        .divider span {
            background: white;
            padding: 0 12px;
            position: relative;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s;
            outline: none;
            box-sizing: border-box;
        }

        .form-group input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }

        .auth-submit {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin: 15px 0 10px;
        }

        .auth-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .error-message, .success-message {
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 13px;
            display: none;
        }

        .error-message {
            background: #fee;
            color: #c33;
        }

        .success-message {
            background: #efe;
            color: #2a7;
        }

        .login-prompt {
            text-align: center;
            background: white;
            padding: 60px 40px;
            border-radius: 15px;
            margin-top: 50px;
        }

        .login-prompt i {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 20px;
        }

        .login-prompt h2 {
            color: #333;
            margin-bottom: 10px;
        }

        .login-prompt p {
            color: #666;
            margin-bottom: 30px;
        }

        .login-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 16px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
.forgot-link {
    text-align: center;
    margin-top: 10px;
}

.forgot-link a {
    color: #667eea;
    text-decoration: none;
    font-size: 13px;
}

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .certificates-grid {
                grid-template-columns: 1fr;
            }

            .cert-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>

        <div class="header">
            <h1>🏆 My Certificates</h1>
            <p>Your achievements and completed courses</p>
            <?php if ($userId && ($userPlan === 'pro' || $userPlan === 'team')): ?>
                <a href="generate_certificate.php" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.2); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin-top: 20px; transition: all 0.3s;">
                    <i class="fas fa-plus"></i> Generate New Certificate
                </a>
            <?php endif; ?>
        </div>

        <?php if (!$userId): ?>
            <!-- Login Prompt for Non-Logged Users -->
            <div class="login-prompt">
                <i class="fas fa-lock"></i>
                <h2>Login Required</h2>
                <p>Please login to view your certificates</p>
                <button class="login-btn" onclick="openAuthModal()">
                    <i class="fas fa-sign-in-alt"></i> Login / Sign Up
                </button>
            </div>
        <?php else: ?>
            <?php if ($userPlan === 'free'): ?>
                <div class="upgrade-notice">
                    <i class="fas fa-crown"></i>
                    Certificates are available for Pro and Max members only!
                    <a href="pricing.php">Upgrade Now</a>
                </div>
            <?php endif; ?>

            <?php if (empty($completedCourses)): ?>
                <div class="no-certificates">
                    <i class="fas fa-certificate"></i>
                    <h2>No Certificates Yet</h2>
                    <p>Complete courses to earn your certificates!</p>
                    <a href="courses.php" class="cert-btn btn-view" style="display: inline-flex; margin-top: 20px;">
                        <i class="fas fa-book"></i> Browse Courses
                    </a>
                </div>
            <?php else: ?>
                <div class="certificates-grid">
                    <?php foreach ($completedCourses as $course): ?>
                        <div class="cert-card">
                            <div class="cert-icon">🏆</div>
                            <h3 class="cert-title">Certificate of Completion</h3>
                            <p class="cert-course"><?php echo htmlspecialchars($course['title']); ?></p>

                            <?php if ($userPlan === 'pro' || $userPlan === 'team'): ?>
                                <div class="cert-actions">
                                    <a href="view_certificate.php?course_id=<?php echo $course['course_id']; ?>" 
                                       class="cert-btn btn-view" target="_blank">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="download_certificate.php?course_id=<?php echo $course['course_id']; ?>" 
                                       class="cert-btn btn-download">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            <?php else: ?>
                                <div style="text-align: center; padding: 15px; background: #fff3cd; border-radius: 8px; color: #856404; margin-top: 15px;">
                                    <i class="fas fa-lock"></i> Upgrade to Pro to download
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Login/Signup Modal -->
    <div id="authModal" class="auth-modal">
        <div class="auth-modal-content">
            <div class="auth-modal-header">
                <span class="modal-close" onclick="closeAuthModal()">&times;</span>
                <div class="modal-logo">CodeLearn</div>
                <div class="modal-subtitle">Access your certificates</div>
            </div>
            
            <div class="auth-forms">
                <div class="form-tabs">
                    <button class="tab-btn active" onclick="showAuthForm('login')">Sign In</button>
                    <button class="tab-btn" onclick="showAuthForm('signup')">Sign Up</button>
                </div>
                
                <div id="errorMessage" class="error-message"></div>
                <div id="successMessage" class="success-message"></div>
                
                <!-- Login Form -->
                <div id="loginForm" class="form-content active">
                    <div class="divider"><span>Enter your credentials</span></div>
                    
                    <form onsubmit="handleAuth(event, 'login')">
                        <div class="form-group">
                            <input type="email" id="loginEmail" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group" style="position: relative;">
                            <input type="password" id="loginPassword" name="password" placeholder="Password" required>
                            <i class="fas fa-eye" id="loginPasswordToggle" 
                            onclick="togglePasswordVisibility('loginPassword', 'loginPasswordToggle')" 
                            style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #999; font-size: 14px;"></i>
                        </div>
                        <button type="submit" class="auth-submit">Sign In</button>
                    </form>
                    <div class="forgot-link">
                    <a href="./forget-pass-page.php">Forgot Password?</a>
                </div>
                </div>
                
                <!-- Signup Form -->
                <div id="signupForm" class="form-content">
                    <div class="divider"><span>Create your account</span></div>
                    
                    <form onsubmit="handleAuth(event, 'signup')">
                        <div class="form-group">
                            <input type="text" id="signupName" name="name" placeholder="Full Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" id="signupEmail" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group" style="position: relative;">
                            <input type="password" id="signupPassword" name="password" placeholder="Password" required minlength="6">
                            <i class="fas fa-eye" id="signupPasswordToggle" 
                            onclick="togglePasswordVisibility('signupPassword', 'signupPasswordToggle')" 
                            style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #999; font-size: 14px;"></i>
                        </div>
                        <button type="submit" class="auth-submit">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Check if user needs to login
        <?php if (!$userId): ?>
        window.addEventListener('load', function() {
            openAuthModal();
        });
        <?php endif; ?>

        function openAuthModal() {
            document.getElementById('authModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeAuthModal() {
            document.getElementById('authModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            clearMessages();
        }

        function showAuthForm(formType) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            document.querySelectorAll('.form-content').forEach(form => form.classList.remove('active'));
            document.getElementById(formType + 'Form').classList.add('active');
            
            clearMessages();
        }

        function showMessage(message, type) {
            clearMessages();
            document.getElementById(type + 'Message').textContent = message;
            document.getElementById(type + 'Message').style.display = 'block';
        }

        function clearMessages() {
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('successMessage').style.display = 'none';
        }

        async function handleAuth(event, action) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            formData.append('action', action);
            
            const submitBtn = form.querySelector('.auth-submit');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = action === 'login' ? 'Signing In...' : 'Signing Up...';
            
            try {
                const response = await fetch('auth.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    setTimeout(() => {
                        window.location.href = 'certificates.php';
                    }, 1500);
                } else {
                    showMessage(result.message, 'error');
                }
                
            } catch (error) {
                showMessage('Network error. Please try again.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        }

        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput && toggleIcon) {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                }
            }
        }

        // Close modal on outside click
        window.addEventListener('click', function(event) {
            const authModal = document.getElementById('authModal');
            if (event.target === authModal) {
                // Don't allow closing if not logged in
                <?php if ($userId): ?>
                closeAuthModal();
                <?php endif; ?>
            }
        });
    </script>
</body>
</html>