<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_POST['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
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
    error_log("DB Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Valid email is required']);
    exit();
}

// Check if user exists with local oauth_provider (not Google accounts)
$stmt = $pdo->prepare("SELECT user_id, name, email, oauth_provider FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Don't reveal if email exists or not (security)
    echo json_encode(['success' => true, 'message' => 'If that email exists, you will receive a reset link.']);
    exit();
}

// Check if it's a Google account
if ($user['oauth_provider'] === 'google') {
    echo json_encode(['success' => false, 'message' => 'This account uses Google Sign-In. Please login with Google.']);
    exit();
}

// Generate secure token
$token = bin2hex(random_bytes(32));
$expire = date('Y-m-d H:i:s', strtotime('+24 hours')); // 24 hours expiry

error_log("Generated token: $token");
error_log("Expire time: $expire");

// Update user with reset token
$updateStmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expire = ? WHERE email = ?");
$updateResult = $updateStmt->execute([$token, $expire, $email]);

if (!$updateResult) {
    error_log("Failed to update token for: $email");
    echo json_encode(['success' => false, 'message' => 'Failed to generate reset link. Please try again.']);
    exit();
}

// Verify token was saved
$verifyStmt = $pdo->prepare("SELECT reset_token, reset_token_expire FROM users WHERE email = ?");
$verifyStmt->execute([$email]);
$verify = $verifyStmt->fetch(PDO::FETCH_ASSOC);
error_log("Token saved in DB: " . ($verify['reset_token'] === $token ? 'YES' : 'NO'));

// Check if PHPMailer exists
if (!file_exists('vendor/phpmailer/phpmailer/src/PHPMailer.php')) {
    error_log("PHPMailer not found");
    echo json_encode(['success' => false, 'message' => 'Email system not configured. Please contact administrator.']);
    exit();
}

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'learnsparktutorial@gmail.com';
    $mail->Password = 'mtis ejbz kqsn zbeq';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    
    $mail->setFrom('learnsparktutorial@gmail.com', 'CodeLearn');
    $mail->addAddress($email, $user['name']);
    
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Request - CodeLearn';
    
    // Build reset link
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . "://" . $host;
    
    // Get the correct path
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    $resetLink = $baseUrl . $scriptPath . "/reset-pass.php?token=" . $token;
    
    error_log("Reset link: $resetLink");
    
    $mail->Body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <h2 style='color: #667eea;'>Password Reset Request</h2>
        <p>Hi <strong>{$user['name']}</strong>,</p>
        <p>You requested to reset your password. Click the button below to proceed:</p>
        <div style='text-align: center; margin: 30px 0;'>
            <a href='{$resetLink}' style='background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a>
        </div>
        <p>Or copy this link:</p>
        <p style='background: #f5f5f5; padding: 10px; word-break: break-all; border-radius: 5px;'>{$resetLink}</p>
        <p>This link will expire in 24 hours.</p>
        <p>If you didn't request this, please ignore this email.</p>
        <hr>
        <p style='color: #999; font-size: 12px;'>CodeLearn - Learning Platform</p>
    </div>
    ";
    
    $mail->send();
    error_log("Email sent successfully to: $email");
    
    echo json_encode([
        'success' => true, 
        'message' => 'Reset link sent to your email!',
        'debug' => 'Token length: ' . strlen($token)
    ]);
    
} catch (Exception $e) {
    error_log("Email error: " . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => 'Failed to send email: ' . $mail->ErrorInfo]);
}
?>