<!-- upgrade.php -->
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$userName = $_SESSION['name'] ?? 'User';
$userPlan = $_SESSION['plan'] ?? 'free';

if ($userPlan === 'pro') {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upgrade to Pro - CodeLearn</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .upgrade-container {
            background: white;
            border-radius: 24px;
            max-width: 500px;
            width: 100%;
            padding: 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .crown-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 12px;
        }

        .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .price-tag {
            background: linear-gradient(135deg, #f6f8ff, #fff9f0);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 30px;
        }

        .price {
            font-size: 48px;
            font-weight: 700;
            color: #333;
        }

        .price span {
            font-size: 24px;
            color: #666;
        }

        .features-list {
            text-align: left;
            margin-bottom: 30px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .feature-item:last-child {
            border-bottom: none;
        }

        .feature-icon {
            color: #10b981;
            margin-right: 12px;
            font-size: 18px;
        }

        .upgrade-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }

        .upgrade-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="upgrade-container">
        <div class="crown-icon">👑</div>
        <h1>Upgrade to Pro</h1>
        <p class="subtitle">Unlock unlimited learning potential</p>

        <div class="price-tag">
            <div class="price">$29<span>/month</span></div>
        </div>

        <div class="features-list">
            <div class="feature-item">
                <i class="fas fa-check-circle feature-icon"></i>
                <span>Access to ALL courses including Pro courses</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-check-circle feature-icon"></i>
                <span>AI-powered learning assistant</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-check-circle feature-icon"></i>
                <span>Verified certificates of completion</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-check-circle feature-icon"></i>
                <span>Priority customer support</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-check-circle feature-icon"></i>
                <span>Downloadable resources and materials</span>
            </div>
        </div>

        <button class="upgrade-btn" onclick="processUpgrade()">
            <i class="fas fa-crown"></i> Upgrade to Pro Now
        </button>

        <a href="index.php" class="back-link">← Back to Home</a>
    </div>

    <script>
        function processUpgrade() {
            // Show processing state
            const btn = document.querySelector('.upgrade-btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            // Simulate payment processing
            setTimeout(() => {
                alert('Payment integration coming soon! This will redirect to a payment gateway.');
                btn.innerHTML = '<i class="fas fa-crown"></i> Upgrade to Pro Now';
                btn.disabled = false;
            }, 2000);
        }
    </script>
</body>
</html>