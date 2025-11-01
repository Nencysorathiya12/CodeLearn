<!-- pricing.php -->
<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['name'] : '';
$userPlan = $isLoggedIn ? ($_SESSION['plan'] ?? 'free') : 'free';
$isWelcome = isset($_GET['welcome']) && $_GET['welcome'] === 'true';
$isNewUser = isset($_GET['new_user']) && $_GET['new_user'] === 'true';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Plans - CodeLearn</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: #f5f5f0;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: white;
            color: #333;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
            margin-bottom: 30px;
        }

        .back-btn:hover {
            background: #f9f9f9;
            transform: translateX(-2px);
        }

        .pricing-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .pricing-header h1 {
            font-size: 36px;
            font-weight: 500;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .pricing-header p {
            color: #666;
            font-size: 16px;
        }

        .plans-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 40px;
        }

        .plan-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 28px 24px;
            position: relative;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .plan-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            transform: translateY(-4px);
        }

        .plan-card.current-plan {
            border-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4, #ffffff);
        }

        .plan-badge {
            position: absolute;
            top: -1px;
            right: -1px;
            background: #000;
            color: white;
            padding: 4px 12px;
            border-radius: 0 12px 0 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .current-badge {
            background: #10b981;
        }

        .plan-icon {
            width: 40px;
            height: 40px;
            margin-bottom: 16px;
        }

        .plan-icon svg {
            width: 100%;
            height: 100%;
        }

        .plan-name {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 6px;
        }

        .plan-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        .plan-price {
            font-size: 36px;
            font-weight: 400;
            color: #1a1a1a;
            margin-bottom: 4px;
            letter-spacing: -1px;
        }

        .plan-price span {
            font-size: 14px;
            color: #666;
            font-weight: 400;
        }

        .billing-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .toggle-label {
            color: #666;
        }

        .toggle-label.active {
            color: #1a1a1a;
            font-weight: 500;
        }

        .save-badge {
            background: #e6f4ff;
            color: #0066cc;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .plan-button {
            width: 100%;
            padding: 12px;
            border: 1px solid #1a1a1a;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 24px;
            background: white;
            color: #1a1a1a;
        }

        .plan-button:hover {
            background: #1a1a1a;
            color: white;
        }

        .plan-button.primary {
            background: #1a1a1a;
            color: white;
        }

        .plan-button.primary:hover {
            background: #333;
        }

        .plan-button.current {
            background: #10b981;
            color: white;
            border-color: #10b981;
            cursor: default;
        }

        .plan-button.current:hover {
            background: #10b981;
        }

        .plan-features {
            list-style: none;
        }

        .features-title {
            font-size: 13px;
            color: #1a1a1a;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .disabled-cursor {
  cursor: not-allowed;
  filter: hue-rotate(330deg) brightness(1.2) saturate(1.8);
}

        .plan-features li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 8px 0;
            color: #4a4a4a;
            font-size: 13px;
            line-height: 1.5;
        }

        .check-icon {
            color: #666;
            font-size: 14px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .feature-highlight {
            color: #0066cc;
            font-weight: 500;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        @media (max-width: 1024px) {
            .plans-container {
                grid-template-columns: 1fr;
                max-width: 450px;
                margin: 40px auto 0;
            }

            .pricing-header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>

        <div class="pricing-header">
            <h1>Choose Your Learning Path</h1>
            <p>Select the plan that best fits your goals</p>
        </div>

        <div class="plans-container">
            <!-- Free Plan -->
            <div class="plan-card <?php echo ($userPlan === 'free' && $isLoggedIn) ? 'current-plan' : ''; ?>">
                <?php if ($userPlan === 'free' && $isLoggedIn): ?>
                    <div class="plan-badge current-badge">Current Plan</div>
                <?php endif; ?>
                
                <div class="plan-icon">
                    <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="24" cy="24" r="20" stroke="#666" stroke-width="2" fill="none"/>
                        <path d="M24 14v10l6 6" stroke="#666" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>

                <h2 class="plan-name">Free</h2>
                <p class="plan-description">Start learning basics</p>
                <p style="font-size: 16px; color: #333; text-align: center;">
                    Get started with zero cost — your learning journey begins here.
                </p>
                
                <div class="plan-price">₹0</div>

                <?php if (!$isLoggedIn): ?>
                    <button class="plan-button" onclick="selectPlan('free')">
                        Start Free
                    </button>
                <?php elseif ($userPlan === 'free' && !$isNewUser): ?>
                    <button class="plan-button current">
                        <i class="fas fa-check"></i> Current Plan
                    </button>
                <?php elseif ($isNewUser): ?>
                    <button class="plan-button" onclick="confirmPlanSelection('free')">
                        Choose Free
                    </button>
                <?php else: ?>
                    <button class="plan-button" onclick="downgradePlan('free')">
                        Switch to Free
                    </button>
                <?php endif; ?>

                <ul class="plan-features">
                    <li><span class="check-icon">✓</span> Access to basic courses</li>
                    <li><span class="check-icon">✓</span> 10+ lessons per course</li>
                    <li><span class="check-icon">✓</span> Live code editor</li>
                    <li><span class="check-icon">✓</span> Quiz assessments</li>
                    <li><span class="check-icon">✓</span> Community support</li>
                    <li><span class="check-icon">✗</span> <span style="text-decoration: line-through; opacity: 0.5;">No certificates</span></li>
                </ul>
            </div>

            <!-- Pro Plan -->
            <div class="plan-card <?php echo ($userPlan === 'pro' && $isLoggedIn) ? 'current-plan' : ''; ?>">
                <?php if ($userPlan === 'pro' && $isLoggedIn): ?>
                    <div class="plan-badge current-badge">Current Plan</div>
                <?php endif; ?>
                
                <div class="plan-icon">
                    <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="24" cy="24" r="20" stroke="#1a1a1a" stroke-width="2.5" fill="none"/>
                        <path d="M20 24l4 4 8-8" stroke="#1a1a1a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h2 class="plan-name">Pro</h2>
                <p class="plan-description">Master programming skills</p>
                
                <div class="plan-price">₹1,499<span>/month</span></div>
                
                <div class="billing-toggle">
                    <span class="toggle-label">Monthly</span>
                    <span class="toggle-label active">Yearly</span>
                    <span class="save-badge">Save 17%</span>
                </div>

                <?php if (!$isLoggedIn): ?>
                    <button class="plan-button primary" onclick="selectPlan('pro')">
                        Get Pro
                    </button>
                <?php elseif ($userPlan === 'pro' && !$isNewUser): ?>
                    <button class="plan-button current">
                        <i class="fas fa-check"></i> Current Plan
                    </button>
                <?php elseif ($isNewUser): ?>
                    <button class="plan-button primary" onclick="confirmPlanSelection('pro')">
                        Choose Pro
                    </button>
                <?php else: ?>
                    <button class="plan-button primary" onclick="upgradePlan('pro')">
                        Upgrade to Pro
                    </button>
                <?php endif; ?>

                <ul class="plan-features">
                    <li class="features-title">Everything in Free, plus:</li>
                    <li><span class="check-icon">✓</span> Access to ALL courses</li>
                    <li><span class="check-icon">✓</span> 50+ lessons per course</li>
                    <li><span class="check-icon">✓</span> Advanced projects</li>
                    <li><span class="check-icon">✓</span> Priority support</li>
                    <li><span class="check-icon feature-highlight">🏆 Verified certificates</span></li>
                    <li><span class="check-icon">✓</span> Downloadable resources</li>
                </ul>
            </div>

            <!-- Max Plan -->
            <div class="plan-card <?php echo ($userPlan === 'team' && $isLoggedIn) ? 'current-plan' : ''; ?>">
                <?php if ($userPlan === 'team' && $isLoggedIn): ?>
                    <div class="plan-badge current-badge">Current Plan</div>
                <?php endif; ?>
                
                <div class="plan-icon">
                    <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="24" cy="24" r="20" stroke="#0066cc" stroke-width="3" fill="none"/>
                        <path d="M18 24l4 4 8-8" stroke="#0066cc" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="24" cy="24" r="10" stroke="#0066cc" stroke-width="2" fill="none"/>
                    </svg>
                </div>

                <h2 class="plan-name">Max</h2>
                <span style="font-size: 14px; color: #777;">Launching soon with MNC collaboration</span>
                <p class="plan-description" style="color: #0066cc;">Complete mastery</p>
                <p style="font-size: 14px; color: #777;">Coming soon after company certification approval</p>

                
                <div class="plan-price" style="color: #0066cc;">₹8,299<span style="color: #666;">/month</span></div>

                <?php if (!$isLoggedIn): ?>
                    <button class="plan-button primary disabled-cursor"  style="background: #0066cc; border-color: #0066cc;">
                    Coming Sooon
                </button>
                <?php elseif ($userPlan === 'team' && !$isNewUser): ?>
                    <button class="plan-button current">
                        <i class="fas fa-check"></i> Current Plan
                    </button>
                <?php elseif ($isNewUser): ?>
                    <button class="plan-button primary disabled-cursor" style="background: #0066cc; border-color: #0066cc;">
                        Coming Soon
                    </button>
                <?php else: ?>
                    <button class="plan-button primary disabled-cursor" style="background: #0066cc; border-color: #0066cc;" onclick="return false;">
                        Coming Soon
                    </button>
                    
                <?php endif; ?>

                <ul class="plan-features">
                    <li class="features-title">Everything in Pro, plus:</li>
                    <li><span class="check-icon">✓</span> Unlimited course access</li>
                    <li><span class="check-icon">✓</span> 100+ lessons per course</li>
                    <li><span class="check-icon">✓</span> Live mentorship sessions</li>
                    <li><span class="check-icon">✓</span> Job placement assistance</li>
                    <li><span class="check-icon feature-highlight">🏆 Premium certificates</span></li>
                    <li><span class="check-icon">✓</span> Lifetime course updates</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
    window.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const isWelcome = urlParams.get('welcome') === 'true';
        const isNewUser = urlParams.get('new_user') === 'true';
        
        if (isWelcome && isNewUser) {
            setTimeout(() => {
                document.querySelector('.plans-container').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }, 500);
        }
    });

    function selectPlan(plan) {
        sessionStorage.setItem('selectedPlan', plan);
        window.location.href = 'index.php#signup';
        setTimeout(() => {
            if (typeof openAuthModal === 'function') {
                openAuthModal();
            }
        }, 500);
    }

    function confirmPlanSelection(plan) {
        if (plan === 'free') {
            const button = event.target;
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            button.disabled = true;

            fetch('update_plan.php?plan=free')
                .then(response => response.text())
                .then(data => {
                    showSuccessMessage('Free plan selected!');
                    setTimeout(() => window.location.href = 'index.php', 1500);
                })
                .catch(error => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                    alert('Error saving plan.');
                });
        } else if (plan === 'pro' || plan === 'team') {
            window.location.href = 'payment.php?plan=' + plan;
        }
    }

    function showSuccessMessage(message) {
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed; top: 20px; right: 20px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white; padding: 14px 20px; border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2); z-index: 10000;
            animation: slideIn 0.3s ease-out; font-weight: 500;
            display: flex; align-items: center; gap: 10px; font-size: 14px;
        `;
        toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 1500);
    }

    function upgradePlan(plan) {
        confirmPlanSelection(plan);
    }

    function downgradePlan(plan) {
        if (confirm('Switch to Free plan? You will lose Pro features.')) {
            confirmPlanSelection(plan);
        }
    }
    </script>
</body>
</html>