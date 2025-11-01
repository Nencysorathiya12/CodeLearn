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

require_once 'vendor/autoload.php';
require_once 'send_invoice.php'; // Include the email sending function

define('STRIPE_SECRET_KEY', 'sk_test_51SE44rL8FC8l6RPuWNS35OEF0sKYLmpdez6X3TrsY7HWK5hzOUUCYgPglpZqaz0X3E2dmL516Q00x1O1ZICOoSXV00ytFAV85M');
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$sessionId = $_GET['session_id'] ?? '';
$plan = $_GET['plan'] ?? '';
$billingCycle = $_GET['billing'] ?? 'monthly';

if (empty($sessionId)) {
    header('Location: pricing.php');
    exit();
}

try {
    $session = \Stripe\Checkout\Session::retrieve($sessionId);
    
    if ($session->payment_status === 'paid') {
        $userId = $_SESSION['user_id'];
        $amount = $session->amount_total / 100; // Convert from paise to rupees
        
        // Get user details from database
        $userStmt = $pdo->prepare("SELECT name, email FROM users WHERE user_id = ?");
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new Exception("User not found");
        }
        
        // Calculate dates
        $startDate = date('Y-m-d');
        $durationMonths = $billingCycle === 'yearly' ? 12 : 1;
        $endDate = date('Y-m-d', strtotime("+{$durationMonths} months"));
        
        // Insert payment record
        $stmt = $pdo->prepare("
            INSERT INTO payment 
            (user_id, course_id, plan, amount, payment_method, transaction_id, status, start_date, end_date, duration_months) 
            VALUES (?, NULL, ?, ?, 'Stripe', ?, 'completed', ?, ?, ?)
        ");
        
        $stmt->execute([
            $userId,
            $plan,
            $amount,
            $sessionId,
            $startDate,
            $endDate,
            $durationMonths
        ]);
        
        // Update user plan
        $updateStmt = $pdo->prepare("UPDATE users SET plan = ? WHERE user_id = ?");
        $updateStmt->execute([$plan, $userId]);
        
        $_SESSION['plan'] = $plan;
        
        // Prepare invoice data for email
        $invoiceData = [
            'transaction_id' => $sessionId,
            'name' => $user['name'],
            'email' => $user['email'],
            'plan' => $plan,
            'billing_cycle' => $billingCycle,
            'duration_months' => $durationMonths,
            'amount' => $amount,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        // Send invoice email
        $emailSent = sendInvoiceEmail($invoiceData);
        
        if ($emailSent) {
            error_log("Invoice email sent successfully to " . $user['email']);
        } else {
            error_log("Failed to send invoice email to " . $user['email']);
        }
        
        // Redirect to success page regardless of email status
        header('Location: payment_success.php?plan=' . $plan);
        exit();
    } else {
        // Payment not completed
        header('Location: pricing.php?error=payment_incomplete');
        exit();
    }
} catch (Exception $e) {
    error_log("Stripe Error: " . $e->getMessage());
    header('Location: pricing.php?error=payment_failed');
    exit();
}
?>