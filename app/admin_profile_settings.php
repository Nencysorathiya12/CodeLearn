<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];
$admin_email = $_SESSION['admin_email'];
$admin_initial = strtoupper(substr($admin_name, 0, 1));

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

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "All fields are required!";
    } elseif (strlen($new_password) < 6) {
        $_SESSION['error_message'] = "New password must be at least 6 characters!";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = "New passwords do not match!";
    } else {
        try {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM admin WHERE admin_id = ?");
            $stmt->execute([$admin_id]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($current_password, $admin['password'])) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE admin_id = ?");
                $stmt->execute([$hashed_password, $admin_id]);
                
                $_SESSION['success_message'] = "Password changed successfully!";
            } else {
                $_SESSION['error_message'] = "Current password is incorrect!";
            }
        } catch(PDOException $e) {
            $_SESSION['error_message'] = "Failed to change password: " . $e->getMessage();
        }
    }
    
    header('Location: admin_profile_settings.php');
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_name = trim($_POST['name']);
    
    if (empty($new_name)) {
        $_SESSION['error_message'] = "Name is required!";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE admin SET name = ? WHERE admin_id = ?");
            $stmt->execute([$new_name, $admin_id]);
            
            $_SESSION['admin_name'] = $new_name;
            $_SESSION['success_message'] = "Profile updated successfully!";
            
            header('Location: admin_profile_settings.php');
            exit();
        } catch(PDOException $e) {
            $_SESSION['error_message'] = "Failed to update profile: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile Settings - CodeLearn</title>
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

        .header {
            background: white;
            padding: 28px 36px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
        }

        .header-title {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #1E293B 0%, #3B82F6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }

        .header-subtitle {
            color: #6B7280;
            font-size: 15px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 30px;
        }

        /* Profile Card */
        .profile-card {
            background: white;
            padding: 32px;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
            text-align: center;
            height: fit-content;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 48px;
            margin: 0 auto 20px;
            box-shadow: 0 12px 24px rgba(59, 130, 246, 0.3);
        }

        .profile-name {
            font-size: 24px;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 8px;
        }

        .profile-email {
            color: #6B7280;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .profile-role {
            display: inline-block;
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 12px;
        }

        /* Settings Forms */
        .settings-card {
            background: white;
            padding: 32px;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #E5E7EB;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: inherit;
        }

        .form-input:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle input {
            padding-right: 45px;
        }

        .toggle-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9CA3AF;
            font-size: 18px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .info-box {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            border-radius: 12px;
            padding: 16px;
            margin-top: 16px;
        }

        .info-box p {
            color: #1E40AF;
            font-size: 13px;
            margin: 0;
        }

        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1 class="header-title">Profile Settings</h1>
        <p class="header-subtitle">Manage your admin account settings and security</p>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <div class="content-grid">
        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-avatar"><?php echo $admin_initial; ?></div>
            <div class="profile-name"><?php echo htmlspecialchars($admin_name); ?></div>
            <div class="profile-email"><?php echo htmlspecialchars($admin_email); ?></div>
            <span class="profile-role">Administrator</span>
            
            <div class="info-box">
                <p><i class="fas fa-info-circle"></i> Admin account with full system access</p>
            </div>
        </div>

        <!-- Settings Forms -->
        <div>
            <!-- Update Profile -->
            <div class="settings-card">
                <h2 class="card-title">
                    <i class="fas fa-user-edit"></i>
                    Update Profile
                </h2>
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-input" value="<?php echo htmlspecialchars($admin_name); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-input" value="<?php echo htmlspecialchars($admin_email); ?>" readonly style="background: #F3F4F6; cursor: not-allowed;">
                        <small style="color: #6B7280; font-size: 12px; margin-top: 4px; display: block;">Email cannot be changed</small>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="settings-card">
                <h2 class="card-title">
                    <i class="fas fa-lock"></i>
                    Change Password
                </h2>
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <div class="password-toggle">
                            <input type="password" id="currentPassword" name="current_password" class="form-input" placeholder="Enter current password" required>
                            <i class="fas fa-eye toggle-icon" onclick="togglePassword('currentPassword', this)"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <div class="password-toggle">
                            <input type="password" id="newPassword" name="new_password" class="form-input" placeholder="Enter new password" required minlength="6">
                            <i class="fas fa-eye toggle-icon" onclick="togglePassword('newPassword', this)"></i>
                        </div>
                        <small style="color: #6B7280; font-size: 12px; margin-top: 4px; display: block;">Minimum 6 characters</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <div class="password-toggle">
                            <input type="password" id="confirmPassword" name="confirm_password" class="form-input" placeholder="Re-enter new password" required minlength="6">
                            <i class="fas fa-eye toggle-icon" onclick="togglePassword('confirmPassword', this)"></i>
                        </div>
                    </div>
                    
                    <button type="submit" name="change_password" class="btn-primary">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
                
                <div class="info-box">
                    <p><i class="fas fa-shield-alt"></i> Use a strong password with letters, numbers, and symbols for better security</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>