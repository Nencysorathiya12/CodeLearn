<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once 'vendor/autoload.php'; // Stripe library

// Stripe credentials
define('STRIPE_SECRET_KEY', 'sk_test_51SE44rL8FC8l6RPuWNS35OEF0sKYLmpdez6X3TrsY7HWK5hzOUUCYgPglpZqaz0X3E2dmL516Q00x1O1ZICOoSXV00ytFAV85M');

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$data = json_decode(file_get_contents('php://input'), true);

$plan = $data['plan'] ?? '';
$billingCycle = $data['billing_cycle'] ?? 'monthly';
$amount = floatval($data['amount'] ?? 0);

if (empty($plan) || $amount <= 0) {
    echo json_encode(['error' => 'Invalid data']);
    exit();
}

try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'inr',
                'product_data' => [
                    'name' => ucfirst($plan) . ' Plan - ' . ucfirst($billingCycle),
                ],
                'unit_amount' => round($amount * 100), // Convert to paise
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost/learning_platform/stripe_success.php?session_id={CHECKOUT_SESSION_ID}&plan=' . $plan . '&billing=' . $billingCycle,
        'cancel_url' => 'http://localhost/learning_platform/payment.php?plan=' . $plan,
        'client_reference_id' => $_SESSION['user_id'],
    ]);

    echo json_encode(['id' => $session->id]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>