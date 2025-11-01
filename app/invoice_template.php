<?php
function generateInvoiceHTML($paymentData) {
    // Validate required data
    $requiredFields = ['transaction_id', 'name', 'email', 'plan', 'billing_cycle', 'duration_months', 'amount', 'end_date'];
    foreach($requiredFields as $field) {
        if(!isset($paymentData[$field])) {
            error_log("Missing required field: $field");
            $paymentData[$field] = 'N/A';
        }
    }
    
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - CodeLearn</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container { 
            max-width: 600px; 
            margin: 20px auto; 
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header { 
            background: linear-gradient(135deg, #667eea, #764ba2); 
            color: white; 
            padding: 40px 30px; 
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header p {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content { 
            padding: 30px;
        }
        .content h2 {
            color: #667eea;
            margin-top: 0;
            font-size: 22px;
        }
        .invoice-box { 
            background: #f9f9f9; 
            padding: 20px; 
            border-radius: 8px; 
            margin: 20px 0;
        }
        .row { 
            display: table;
            width: 100%;
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .row:last-child {
            border-bottom: none;
        }
        .row span:first-child {
            display: table-cell;
            font-weight: 600;
            color: #555;
        }
        .row span:last-child {
            display: table-cell;
            text-align: right;
            color: #333;
        }
        .total { 
            font-size: 20px; 
            font-weight: bold; 
            color: #667eea; 
            background: linear-gradient(135deg, #e6f0ff, #f0e6ff);
            padding: 20px; 
            border-radius: 8px; 
            margin-top: 20px;
        }
        .total-row {
            display: table;
            width: 100%;
        }
        .total-row span {
            display: table-cell;
        }
        .total-row span:last-child {
            text-align: right;
        }
        .info-box {
            background: #e6f4ff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            border-left: 4px solid #667eea;
        }
        .info-box strong {
            color: #667eea;
            font-size: 16px;
            display: block;
            margin-bottom: 10px;
        }
        .info-box ul {
            margin: 10px 0;
            padding-left: 20px;
            line-height: 1.8;
        }
        .info-box li {
            color: #555;
        }
        .cta-button {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button a {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            font-weight: bold;
            font-size: 16px;
            transition: transform 0.2s;
        }
        .footer {
            text-align: center;
            padding: 30px;
            background: #f9f9f9;
            color: #666;
            font-size: 13px;
            border-top: 1px solid #e0e0e0;
        }
        .footer p {
            margin: 8px 0;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Payment Successful!</h1>
            <p>Thank you for choosing CodeLearn</p>
        </div>
        
        <div class="content">
            <h2>Invoice Details</h2>
            
            <div class="invoice-box">
                <div class="row">
                    <span>Invoice Number:</span>
                    <span>#INV-' . htmlspecialchars($paymentData['transaction_id']) . '</span>
                </div>
                <div class="row">
                    <span>Date:</span>
                    <span>' . date('F j, Y') . '</span>
                </div>
                <div class="row">
                    <span>Customer Name:</span>
                    <span>' . htmlspecialchars($paymentData['name']) . '</span>
                </div>
                <div class="row">
                    <span>Email:</span>
                    <span>' . htmlspecialchars($paymentData['email']) . '</span>
                </div>
                <div class="row">
                    <span>Plan:</span>
                    <span>' . strtoupper(htmlspecialchars($paymentData['plan'])) . ' Plan</span>
                </div>
                <div class="row">
                    <span>Billing Cycle:</span>
                    <span>' . ucfirst(htmlspecialchars($paymentData['billing_cycle'])) . '</span>
                </div>
                <div class="row">
                    <span>Duration:</span>
                    <span>' . htmlspecialchars($paymentData['duration_months']) . ' month(s)</span>
                </div>
                <div class="row">
                    <span>Valid Until:</span>
                    <span>' . date('F j, Y', strtotime($paymentData['end_date'])) . '</span>
                </div>
                
                <div class="total">
                    <div class="total-row">
                        <span>Total Amount Paid:</span>
                        <span>₹' . number_format($paymentData['amount'], 2) . '</span>
                    </div>
                </div>
            </div>
            
            <div class="info-box">
                <strong>✓ What\'s Next?</strong>
                <ul>
                    <li>Your account has been upgraded to ' . strtoupper(htmlspecialchars($paymentData['plan'])) . ' plan</li>
                    <li>Access all premium features immediately</li>
                    <li>Start learning from 100+ courses</li>
                    <li>Download certificates upon completion</li>
                </ul>
            </div>
            
            <div class="cta-button">
                <a href="http://localhost/learning_platform/index.php">Go to Dashboard</a>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>CodeLearn</strong></p>
            <p>This is an automated invoice. Please keep it for your records.</p>
            <p>For support, contact us at support@codelearn.com</p>
            <p>&copy; ' . date('Y') . ' CodeLearn. All rights reserved.</p>
        </div>
    </div>
</body>
</html>';
    
    return $html;
}
?>