<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Database connection
require_once 'payment_config.php'; // Payment-specific config

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$razorpayPaymentId = $input['razorpay_payment_id'] ?? '';
$razorpayOrderId = $input['razorpay_order_id'] ?? '';
$razorpaySignature = $input['razorpay_signature'] ?? '';
$plan = $input['plan'] ?? '';
$amount = $input['amount'] ?? 0;

// Razorpay credentials
$razorpayKeySecret = RAZORPAY_KEY_SECRET;

// Verify payment signature
$generated_signature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $razorpayKeySecret);

if ($generated_signature !== $razorpaySignature) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment signature']);
    exit();
}

// Payment is verified, now update database
$userId = $_SESSION['user_id'];

// Convert plan name (team -> max in database)
$dbPlan = ($plan === 'team') ? 'max' : $plan;

// Calculate subscription dates
$startDate = date('Y-m-d');
$durationMonths = 1; // Default to 1 month
$endDate = date('Y-m-d', strtotime("+{$durationMonths} months"));

try {
    // Start transaction
    $pdo->beginTransaction();

    // Insert payment record
    $stmt = $pdo->prepare("
        INSERT INTO payment 
        (user_id, course_id, plan, amount, payment_date, payment_method, transaction_id, status, start_date, end_date, duration_months)
        VALUES (?, NULL, ?, ?, NOW(), 'Razorpay', ?, 'completed', ?, ?, ?)
    ");
    
    $stmt->execute([
        $userId,
        $dbPlan,
        $amount,
        $razorpayPaymentId,
        $startDate,
        $endDate,
        $durationMonths
    ]);

    // Update user's plan in users table
    $updateUserStmt = $pdo->prepare("UPDATE users SET plan = ? WHERE user_id = ?");
    $updateUserStmt->execute([$dbPlan, $userId]);

    // Update session
    $_SESSION['plan'] = $dbPlan;

    // Commit transaction
    $pdo->commit();

    // Log successful payment (optional)
    error_log("Payment successful - User: $userId, Plan: $dbPlan, Amount: $amount, TxnID: $razorpayPaymentId");
    
    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Payment successful',
        'plan' => $dbPlan,
        'transaction_id' => $razorpayPaymentId,
        'redirect_url' => 'payment_success.php'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollback();
    
    // Log error
    error_log("Payment verification error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}
?>