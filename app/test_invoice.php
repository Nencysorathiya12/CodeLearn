<?php
// Test file banao: test_invoice.php
require_once 'send_invoice.php'; // ya jo bhi aapki file ka naam hai

// Test data
$testData = [
    'email' => 'learnsparktutorial@gmail.com', // apna email daalo
    'name' => 'Test User',
    'invoice_number' => 'INV-001',
    'amount' => 1000,
    'date' => date('Y-m-d')
];

echo "Testing email function...<br><br>";

$result = sendInvoiceEmail($testData);

if ($result) {
    echo "✓ Email sent successfully!";
} else {
    echo "✗ Email failed!";
}

// Check error log
echo "<br><br>Check PHP error log for details.";
?>