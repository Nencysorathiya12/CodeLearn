<!-- process-payment.php -->
<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
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
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// PayPal credentials
define('PAYPAL_CLIENT_ID', 'YOUR_PAYPAL_CLIENT_ID_HERE');
define('PAYPAL_SECRET', 'YOUR_PAYPAL_SECRET_HERE');
define('PAYPAL_MODE', 'sandbox'); // sandbox or live

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['paypal_order_id'] ?? '';
    $plan = $_POST['plan'] ?? '';
    $billingCycle = $_POST['billing_cycle'] ?? 'monthly';
    $amount = floatval($_POST['amount'] ?? 0);
    $userId = $_SESSION['user_id'];

    if (empty($orderId) || empty($plan) || $amount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid payment data']);
        exit();
    }

    // PayPal API URL
    $apiUrl = PAYPAL_MODE === 'sandbox' 
        ? 'https://api-m.sandbox.paypal.com' 
        : 'https://api-m.paypal.com';

    // Verify payment with PayPal
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl . "/v2/checkout/orders/" . $orderId);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    $orderData = json_decode($response, true);

    if ($orderData && isset($orderData['status']) && $orderData['status'] === 'COMPLETED') {
        try {
            // Calculate subscription dates
            $startDate = date('Y-m-d');
            $durationMonths = $billingCycle === 'yearly' ? 12 : 1;
            $endDate = date('Y-m-d', strtotime("+{$durationMonths} months"));

            // Insert payment record
            $stmt = $pdo->prepare("
                INSERT INTO payment 
                (user_id, course_id, plan, amount, payment_method, transaction_id, status, start_date, end_date, duration_months) 
                VALUES (?, NULL, ?, ?, 'PayPal', ?, 'completed', ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId,
                $plan,
                $amount,
                $orderId,
                $startDate,
                $endDate,
                $durationMonths
            ]);

            // Update user plan
            $updateStmt = $pdo->prepare("UPDATE users SET plan = ? WHERE user_id = ?");
            $updateStmt->execute([$plan, $userId]);

            // Update session
            $_SESSION['plan'] = $plan;

            echo json_encode([
                'success' => true,
                'message' => 'Payment successful',
                'transaction_id' => $orderId
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to process payment: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>