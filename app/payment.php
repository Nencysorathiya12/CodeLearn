<!-- payment.php -->
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

// Get plan from URL
$selectedPlan = $_GET['plan'] ?? 'pro';
if (!in_array($selectedPlan, ['pro', 'team'])) {
    header('Location: pricing.php');
    exit();
}

// Plan details
$planDetails = [
    'pro' => [
        'name' => 'Pro Plan',
        'monthly_price' => 1499,
        'yearly_price' => 14990,
        'features' => [
            'Access to ALL courses',
            '50+ lessons per course',
            'Advanced projects',
            'Priority support',
            'Verified certificates',
            'Downloadable resources'
        ]
    ],
    'team' => [
        'name' => 'Max Plan',
        'monthly_price' => 8299,
        'yearly_price' => 82990,
        'features' => [
            'Unlimited course access',
            '100+ lessons per course',
            'Live mentorship sessions',
            'Job placement assistance',
            'Premium certificates',
            'Lifetime course updates'
        ]
    ]
];

$plan = $planDetails[$selectedPlan];
$userName = $_SESSION['name'];
$userEmail = $_SESSION['email'];
$userId = $_SESSION['user_id'];

// Stripe credentials
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51SE44rL8FC8l6RPuDHL8U4aerqhmPjU1C8Ax5DdfZwVAmnQt987eYUT9A12sTZ5G8UXcsEJLaOpsrUvFR5j5SU1V005O1KTZqk');
define('STRIPE_SECRET_KEY', 'sk_test_51SE44rL8FC8l6RPuWNS35OEF0sKYLmpdez6X3TrsY7HWK5hzOUUCYgPglpZqaz0X3E2dmL516Q00x1O1ZICOoSXV00ytFAV85M');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - <?php echo $plan['name']; ?> - CodeLearn</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>    
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
            max-width: 800px;
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

        .payment-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .payment-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .payment-header h1 {
            font-size: 28px;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .payment-header p {
            color: #666;
            font-size: 14px;
        }

        .plan-summary {
            background: #f9f9f9;
            padding: 24px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .plan-name {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 16px;
        }

        .billing-options {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .billing-option {
            flex: 1;
            padding: 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .billing-option:hover {
            border-color: #1a1a1a;
        }

        .billing-option.active {
            border-color: #1a1a1a;
            background: #f0f0f0;
        }

        .billing-option input {
            position: absolute;
            opacity: 0;
        }

        .billing-label {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .billing-price {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .billing-save {
            background: #10b981;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }

        .features-list {
            list-style: none;
            margin-top: 20px;
        }

        .features-list li {
            padding: 8px 0;
            color: #666;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .check-icon {
            color: #10b981;
        }

        .price-breakdown {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 2px solid #e0e0e0;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
        }

        .price-row.total {
            font-size: 20px;
            font-weight: 700;
            padding-top: 16px;
            margin-top: 16px;
            border-top: 2px solid #e0e0e0;
        }

        .payment-btn {
            width: 100%;
            padding: 16px;
            background: #1a1a1a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 30px;
        }

        .payment-btn:hover {
            background: #333;
        }

        .payment-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .secure-badge {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .lock-icon {
            color: #10b981;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="pricing.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Pricing
        </a>

        <div class="payment-card">
            <div class="payment-header">
                <h1>Complete Your Purchase</h1>
                <p>Secure payment powered by Stripe</p>            
            </div>

            <div class="plan-summary">
                <div class="plan-name"><?php echo $plan['name']; ?></div>

                <div class="billing-options">
                    <label class="billing-option active" id="monthly-option">
                        <input type="radio" name="billing" value="monthly" checked>
                        <div class="billing-label">Monthly</div>
                        <div class="billing-price">₹<?php echo number_format($plan['monthly_price']); ?></div>
                    </label>

                    <label class="billing-option" id="yearly-option">
                        <input type="radio" name="billing" value="yearly">
                        <div class="billing-label">
                            Yearly
                            <span class="billing-save">Save 17%</span>
                        </div>
                        <div class="billing-price">₹<?php echo number_format($plan['yearly_price']); ?></div>
                    </label>
                </div>

                <ul class="features-list">
                    <?php foreach ($plan['features'] as $feature): ?>
                        <li>
                            <i class="fas fa-check-circle check-icon"></i>
                            <?php echo $feature; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="price-breakdown">
                    <div class="price-row">
                        <span>Subtotal</span>
                        <span id="subtotal">₹<?php echo number_format($plan['monthly_price']); ?></span>
                    </div>
                    <div class="price-row">
                        <span>GST (18%)</span>
                        <span id="gst">₹<?php echo number_format($plan['monthly_price'] * 0.18); ?></span>
                    </div>
                    <div class="price-row total">
                        <span>Total Amount</span>
                        <span id="total">₹<?php echo number_format($plan['monthly_price'] * 1.18); ?></span>
                    </div>
                </div>
            </div>

            <button class="payment-btn" id="pay-btn">
                <i class="fas fa-lock"></i> Proceed to Payment
            </button>

            <div class="secure-badge">
                <i class="fas fa-shield-alt lock-icon"></i>
                Secured by Stripe - Your payment information is safe
            </div>
        </div>
    </div>

    <script>
        const monthlyPrice = <?php echo $plan['monthly_price']; ?>;
        const yearlyPrice = <?php echo $plan['yearly_price']; ?>;
        let currentBilling = 'monthly';
        let currentAmount = monthlyPrice;

        // Handle billing toggle
        document.querySelectorAll('.billing-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.billing-option').forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                this.querySelector('input').checked = true;
                
                currentBilling = this.querySelector('input').value;
                currentAmount = currentBilling === 'monthly' ? monthlyPrice : yearlyPrice;
                
                updatePriceDisplay();
            });
        });

        function updatePriceDisplay() {
            const subtotal = currentAmount;
            const gst = subtotal * 0.18;
            const total = subtotal + gst;

            document.getElementById('subtotal').textContent = '₹' + subtotal.toLocaleString('en-IN');
            document.getElementById('gst').textContent = '₹' + gst.toLocaleString('en-IN', {maximumFractionDigits: 0});
            document.getElementById('total').textContent = '₹' + total.toLocaleString('en-IN', {maximumFractionDigits: 0});
        }

        // Stripe Payment
const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');

document.getElementById('pay-btn').addEventListener('click', async function() {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    try {
        // Create payment intent
        const response = await fetch('create_stripe_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                plan: '<?php echo $selectedPlan; ?>',
                billing_cycle: currentBilling,
                amount: currentAmount * 1.18
            })
        });

        const session = await response.json();

        if (session.error) {
            alert(session.error);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-lock"></i> Proceed to Payment';
            return;
        }

        // Redirect to Stripe Checkout
        const result = await stripe.redirectToCheckout({
            sessionId: session.id
        });

        if (result.error) {
            alert(result.error.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-lock"></i> Proceed to Payment';
        }
    } catch (error) {
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-lock"></i> Proceed to Payment';
    }
});
</script>
</body>
</html>