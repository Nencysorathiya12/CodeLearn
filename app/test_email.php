<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

echo "<h2>Email Configuration Test</h2>";

// Step 1: Check PHPMailer installation
echo "<h3>1. PHPMailer Check:</h3>";
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "✓ PHPMailer is installed<br>";
} else {
    echo "✗ PHPMailer NOT found. Run: composer require phpmailer/phpmailer<br>";
    exit;
}

// Step 2: Check invoice template
echo "<h3>2. Template File Check:</h3>";
if (file_exists('invoice_template.php')) {
    echo "✓ invoice_template.php exists<br>";
    require_once 'invoice_template.php';
    if (function_exists('generateInvoiceHTML')) {
        echo "✓ generateInvoiceHTML() function found<br>";
    } else {
        echo "✗ generateInvoiceHTML() function NOT found<br>";
    }
} else {
    echo "✗ invoice_template.php NOT found<br>";
}

// Step 3: Test email sending
echo "<h3>3. Sending Test Email:</h3>";

$mail = new PHPMailer(true);

try {
    // Enable verbose debug output
    $mail->SMTPDebug = 3;
    $mail->Debugoutput = 'html';
    
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'learnsparktutorial@gmail.com';
    $mail->Password = 'mtis ejbz kqsn zbeq';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Disable SSL verification (for testing only)
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
    $mail->addAddress('learnsparktutorial@gmail.com'); // Send to yourself for testing
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email - ' . date('Y-m-d H:i:s');
    $mail->Body = '<h1>This is a test email</h1><p>If you receive this, email is working!</p>';
    $mail->AltBody = 'This is a test email. If you receive this, email is working!';
    
    echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0;'>";
    $mail->send();
    echo "</div>";
    
    echo "<p style='color: green; font-weight: bold;'>✓ EMAIL SENT SUCCESSFULLY!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>✗ EMAIL FAILED!</p>";
    echo "<p><strong>Error:</strong> {$mail->ErrorInfo}</p>";
    echo "<p><strong>Exception:</strong> {$e->getMessage()}</p>";
}

echo "<hr>";
echo "<h3>4. PHP Configuration:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "OpenSSL: " . (extension_loaded('openssl') ? '✓ Enabled' : '✗ Disabled') . "<br>";
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? '✓ Enabled' : '✗ Disabled') . "<br>";

echo "<hr>";
echo "<h3>5. Alternative Solutions:</h3>";
echo "<ul>";
echo "<li><strong>Option 1:</strong> Use SMTP2GO, SendGrid, or Mailgun (more reliable than Gmail)</li>";
echo "<li><strong>Option 2:</strong> Use PHP mail() function (if server supports)</li>";
echo "<li><strong>Option 3:</strong> Check if your hosting blocks port 587 (try port 465 with SSL)</li>";
echo "</ul>";
?>