<!-- update-plan.php -->
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
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

$newPlan = $_GET['plan'] ?? 'free';
$userId = $_SESSION['user_id'];

// Validate plan - only allow 'free' without payment
if ($newPlan !== 'free') {
    header('Location: payment.php?plan=' . $newPlan);
    exit();
}

// Update user plan to free
$stmt = $pdo->prepare("UPDATE users SET plan = 'free' WHERE user_id = ?");
$stmt->execute([$userId]);

// Update session
$_SESSION['plan'] = 'free';

// Success response
echo "<!DOCTYPE html>
<html>
<head>
    <title>Plan Updated</title>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .success-box {
            background: white;
            padding: 50px 40px;
            border-radius: 24px;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 100%;
        }
        .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.5s ease-out;
        }
        .icon i {
            font-size: 40px;
            color: white;
        }
        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        h1 {
            color: #1a1a1a;
            font-size: 28px;
            margin-bottom: 12px;
        }
        p {
            color: #666;
            margin-bottom: 24px;
            line-height: 1.6;
        }
        .plan-badge {
            background: #f0f0f0;
            color: #1a1a1a;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            font-weight: 600;
            margin-bottom: 24px;
        }
        .redirect-info {
            color: #999;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #e0e0e0;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class='success-box'>
        <div class='icon'>
            <i class='fas fa-check'></i>
        </div>
        <h1>Welcome to CodeLearn!</h1>
        <p>Your free plan has been activated successfully. Start your learning journey today!</p>
        <div class='plan-badge'>
            <i class='fas fa-clock'></i> Free Plan Active
        </div>
        <p class='redirect-info'>
            <span class='spinner'></span>
            Redirecting to homepage...
        </p>
    </div>
    <script>
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 2000);
    </script>
</body>
</html>";
?>