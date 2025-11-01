<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codelearn_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$token = $_GET['token'] ?? '';
$error = '';
$tokenValid = false;
$user = null;

// DEBUG: Log token check
error_log("Token from URL: " . $token);

// Verify token
if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT user_id, name, email, reset_token, reset_token_expire FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // DEBUG: Log query result
    error_log("User found: " . ($user ? 'YES' : 'NO'));
    
    if ($user) {
        error_log("Token in DB: " . $user['reset_token']);
        error_log("Expire time: " . $user['reset_token_expire']);
        error_log("Current time: " . date('Y-m-d H:i:s'));
        
        // Check if token is expired
        $currentTime = time();
        $expireTime = strtotime($user['reset_token_expire']);
        
        if ($currentTime > $expireTime) {
            $error = 'This reset link has expired. Token was valid until ' . date('M d, Y H:i', $expireTime);
            error_log("Token EXPIRED");
        } else {
            $tokenValid = true;
            error_log("Token VALID");
        }
    } else {
        // Check if token exists at all in database
        $checkStmt = $pdo->prepare("SELECT COUNT(*) as count, MAX(reset_token_expire) as last_expire FROM users WHERE reset_token IS NOT NULL");
        $checkStmt->execute();
        $dbCheck = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("Active tokens in DB: " . $dbCheck['count']);
        error_log("Last expire time: " . $dbCheck['last_expire']);
        
        $error = 'Invalid reset link. Please request a new one.';
    }
} else {
    $error = 'No reset token provided';
}

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $token = $_POST['token'] ?? '';
    
    error_log("POST request - Token: " . $token);
    
    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit();
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit();
    }
    
    // Verify token again before updating
    $checkStmt = $pdo->prepare("SELECT user_id, email FROM users WHERE reset_token = ? AND reset_token_expire > NOW()");
    $checkStmt->execute([$token]);
    $userCheck = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$userCheck) {
        error_log("Token validation failed in POST");
        echo json_encode(['success' => false, 'message' => 'Token expired or invalid. Please request a new reset link.']);
        exit();
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Update password and clear token
    $updateStmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expire = NULL WHERE reset_token = ?");
    
    if ($updateStmt->execute([$hashedPassword, $token])) {
        error_log("Password updated successfully for: " . $userCheck['email']);
        echo json_encode(['success' => true, 'message' => 'Password reset successfully!']);
    } else {
        error_log("Failed to update password");
        echo json_encode(['success' => false, 'message' => 'Failed to reset password. Please try again.']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CodeLearn</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo i {
            font-size: 64px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        .form-group input {
            width: 100%;
            padding: 16px 50px 16px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            font-size: 18px;
        }
        .toggle-password:hover {
            color: #667eea;
        }
        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .message {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
        }
        .back-link {
            text-align: center;
            margin-top: 25px;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 15px;
        }
        .password-requirements {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #666;
        }
        .debug-info {
            background: #fff3cd;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 12px;
            font-family: monospace;
            color: #856404;
            max-height: 150px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <i class="fas fa-key"></i>
        </div>
        <h2>Reset Password</h2>
        <p class="subtitle">Create a new strong password</p>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            
            <!-- DEBUG INFO -->
            <div class="debug-info">
                <strong>Debug Info:</strong><br>
                Token Length: <?php echo strlen($token); ?><br>
                Current Server Time: <?php echo date('Y-m-d H:i:s'); ?><br>
                <?php if ($user): ?>
                    Expire Time: <?php echo $user['reset_token_expire']; ?><br>
                    Time Difference: <?php echo round((strtotime($user['reset_token_expire']) - time()) / 60); ?> minutes
                <?php endif; ?>
            </div>
            
            <div class="back-link">
                <a href="forget-pass-page.php"><i class="fas fa-arrow-left"></i> Request New Link</a>
            </div>
        <?php else: ?>
            <div id="message" style="display: none;" class="message"></div>
            
            <form id="resetForm">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="password-requirements">
                    <strong>Password must contain:</strong> At least 6 characters
                </div>
                
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="New Password" required minlength="6">
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('password')"></i>
                </div>
                
                <div class="form-group">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm New Password" required minlength="6">
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password')"></i>
                </div>
                
                <button type="submit" class="submit-btn">Reset Password</button>
            </form>
            
            <div class="back-link">
                <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Sign In</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling;
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('.submit-btn');
            const messageDiv = document.getElementById('message');
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Resetting...';
            messageDiv.style.display = 'none';
            
            fetch('reset-pass.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                messageDiv.className = 'message ' + (result.success ? 'success' : 'error');
                messageDiv.textContent = result.message;
                messageDiv.style.display = 'block';
                
                if (result.success) {
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                }
            })
            .catch(error => {
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Network error. Please try again.';
                messageDiv.style.display = 'block';
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Reset Password';
            });
        });
    </script>
</body>
</html>