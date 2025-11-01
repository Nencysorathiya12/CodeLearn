<!-- paymet-sucess.php -->
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$plan = $_GET['plan'] ?? 'pro';
$planNames = [
    'pro' => 'Pro Plan',
    'team' => 'Max Plan'
];
$planName = $planNames[$plan] ?? 'Pro Plan';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - CodeLearn</title>
    <meta http-equiv="refresh" content="10;url=index.php">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            background: white;
            border-radius: 24px;
            max-width: 500px;
            width: 100%;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out;
        }

        .success-icon i {
            font-size: 50px;
            color: white;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        h1 {
            font-size: 32px;
            color: #1a1a1a;
            margin-bottom: 12px;
        }

        .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .plan-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            display: inline-block;
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 30px;
        }

        .benefits-list {
            text-align: left;
            background: #f9f9f9;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .benefits-title {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 16px;
            font-size: 16px;
        }

        .benefit-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
            color: #666;
            font-size: 14px;
        }

        .benefit-icon {
            color: #10b981;
            font-size: 16px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 14px 24px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #1a1a1a;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: #333;
        }

        .btn-secondary {
            background: white;
            color: #1a1a1a;
            border: 2px solid #1a1a1a;
        }

        .btn-secondary:hover {
            background: #f9f9f9;
        }

        .confetti {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1>Payment Successful! 🎉</h1>
        <p class="subtitle">
            Welcome to your premium learning journey!<br>
            Your account has been upgraded successfully.
        </p>

        <div class="plan-badge">
            <i class="fas fa-crown"></i> <?php echo $planName; ?> Activated
        </div>

        <div class="benefits-list">
            <div class="benefits-title">What's unlocked for you:</div>
            <div class="benefit-item">
                <i class="fas fa-check-circle benefit-icon"></i>
                <span>Instant access to all premium courses</span>
            </div>
            <div class="benefit-item">
                <i class="fas fa-check-circle benefit-icon"></i>
                <span>Verified certificates upon completion</span>
            </div>
            <div class="benefit-item">
                <i class="fas fa-check-circle benefit-icon"></i>
                <span>Priority support from our team</span>
            </div>
            <div class="benefit-item">
                <i class="fas fa-check-circle benefit-icon"></i>
                <span>Downloadable resources and materials</span>
            </div>
        </div>

        <div class="action-buttons">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Go to Dashboard
            </a>
            <a href="courses.php" class="btn btn-secondary">
                <i class="fas fa-book"></i> Browse Courses
            </a>
        </div>
    </div>

    <canvas class="confetti" id="confetti-canvas"></canvas>

    <script>
        // Simple confetti animation
        const canvas = document.getElementById('confetti-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const confettiCount = 100;
        const confetti = [];

        for (let i = 0; i < confettiCount; i++) {
            confetti.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height - canvas.height,
                size: Math.random() * 6 + 4,
                speed: Math.random() * 3 + 2,
                color: ['#667eea', '#764ba2', '#10b981', '#f59e0b', '#ef4444'][Math.floor(Math.random() * 5)]
            });
        }

        function drawConfetti() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            confetti.forEach((c, index) => {
                ctx.fillStyle = c.color;
                ctx.fillRect(c.x, c.y, c.size, c.size);
                
                c.y += c.speed;
                c.x += Math.sin(c.y / 30) * 2;
                
                if (c.y > canvas.height) {
                    confetti[index] = {
                        x: Math.random() * canvas.width,
                        y: -10,
                        size: Math.random() * 6 + 4,
                        speed: Math.random() * 3 + 2,
                        color: c.color
                    };
                }
            });
            
            requestAnimationFrame(drawConfetti);
        }

        drawConfetti();

        // Stop confetti after 5 seconds
        setTimeout(() => {
            canvas.style.opacity = '0';
            canvas.style.transition = 'opacity 1s';
        }, 5000);
    </script>
</body>
</html>