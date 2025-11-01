<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendInvoiceEmail($invoiceData) {
    $mail = new PHPMailer(true);
    
    try {
        // Enable verbose debug output (remove in production)
        $mail->SMTPDebug = 2; // 0 = off, 1 = client, 2 = client and server
        $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer Debug level $level: $str");
        };
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'learnsparktutorial@gmail.com';
        $mail->Password = 'mtis ejbz kqsn zbeq'; // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Additional SMTP settings for better compatibility
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Set timeout
        $mail->Timeout = 30;
        
        // Recipients
        $mail->setFrom('learnsparktutorial@gmail.com', 'CodeLearn');
        $mail->addAddress($invoiceData['email'], $invoiceData['name']);
        
        // Optional: Add reply-to
        $mail->addReplyTo('learnsparktutorial@gmail.com', 'CodeLearn Support');
        
        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Payment Successful - Invoice #' . $invoiceData['transaction_id'];
        
        // Generate invoice HTML
        require_once 'invoice_template.php';
        $mail->Body = generateInvoiceHTML($invoiceData);
        
        // Plain text version for non-HTML email clients
        $mail->AltBody = "Thank you for your payment!\n\n" .
                         "Invoice Number: #INV-" . $invoiceData['transaction_id'] . "\n" .
                         "Customer: " . $invoiceData['name'] . "\n" .
                         "Email: " . $invoiceData['email'] . "\n" .
                         "Plan: " . strtoupper($invoiceData['plan']) . "\n" .
                         "Amount: ₹" . number_format($invoiceData['amount'], 2) . "\n\n" .
                         "Visit: http://localhost/learning_platform/index.php";
        
        // Send email
        if($mail->send()) {
            error_log("Email sent successfully to: " . $invoiceData['email']);
            return [
                'success' => true,
                'message' => 'Invoice email sent successfully!'
            ];
        }
        
    } catch (Exception $e) {
        error_log("Email Error: " . $e->getMessage());
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
        
        return [
            'success' => false,
            'message' => 'Email could not be sent.',
            'error' => $mail->ErrorInfo
        ];
    }
}

// Example usage:
// $paymentData = [
//     'transaction_id' => 'TXN123456789',
//     'name' => 'John Doe',
//     'email' => 'user@example.com',
//     'plan' => 'premium',
//     'billing_cycle' => 'monthly',
//     'duration_months' => 1,
//     'amount' => 499.00,
//     'end_date' => date('Y-m-d', strtotime('+1 month'))
// ];
// 
// $result = sendInvoiceEmail($paymentData);
// if($result['success']) {
//     echo "Email sent successfully!";
// } else {
//     echo "Error: " . $result['error'];
// }
?>