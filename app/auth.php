<?php
session_start();
header('Content-Type: application/json');

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
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Razorpay Configuration
define('RAZORPAY_KEY_ID', 'rzp_test_YOUR_KEY_ID_HERE');
define('RAZORPAY_KEY_SECRET', 'YOUR_SECRET_KEY_HERE');
define('RAZORPAY_ENV', 'test');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required']);
            exit();
        }
        
        // Check admin table first
        try {
            $tableCheck = $pdo->query("SHOW TABLES LIKE 'admin'");
            if ($tableCheck->rowCount() > 0) {
                $stmt = $pdo->prepare("SELECT admin_id, name, email, password FROM admin WHERE email = ?");
                $stmt->execute([$email]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($admin) {
                    $passwordMatch = false;
                    
                    if (password_verify($password, $admin['password'])) {
                        $passwordMatch = true;
                    } elseif ($password === $admin['password']) {
                        $passwordMatch = true;
                    } elseif (($password === 'admin@123' && $admin['password'] === 'admin@123') || 
                            ($password === 'admin123' && $admin['password'] === 'admin123')) {
                        $passwordMatch = true;
                    }
                    
                    if ($passwordMatch) {
                        $_SESSION['admin_id'] = $admin['admin_id'];
                        $_SESSION['admin_name'] = $admin['name'];
                        $_SESSION['admin_email'] = $admin['email'];
                        $_SESSION['user_type'] = 'admin';
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Admin login successful!',
                            'redirect' => 'admin-panel.php',
                            'user_type' => 'admin'
                        ]);
                        exit();
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Admin login error: " . $e->getMessage());
        }
        
        // Check users table
        try {
            $stmt = $pdo->prepare("SELECT user_id, name, email, password, plan FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['plan'] = $user['plan'] ?? 'free';
                $_SESSION['user_type'] = 'user';
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => 'index.php',
                    'user_type' => 'user'
                ]);
                exit();
            }
        } catch (Exception $e) {
            error_log("User login error: " . $e->getMessage());
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        
    } elseif ($action === 'signup') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($name) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit();
        }
        
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
            exit();
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit();
        }
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ? UNION SELECT email FROM admin WHERE email = ?");
        $stmt->execute([$email, $email]);
        
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            exit();
        }
        
        // Hash password and insert user with default 'free' plan
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, plan, oauth_provider, email_verified, created_at) VALUES (?, ?, ?, 'free', 'local', TRUE, NOW())");
        
        if ($stmt->execute([$name, $email, $hashedPassword])) {
            $userId = $pdo->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['plan'] = 'free';
            $_SESSION['user_type'] = 'user';
            
            // Redirect to pricing page for plan selection
            echo json_encode([
                'success' => true, 
                'message' => 'Account created successfully!',
                'redirect' => 'pricing.php?welcome=true&new_user=true'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create account']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>