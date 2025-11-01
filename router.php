<?php
/**
 * CodeLearn LMS - Router
 * Handles all URL routing and page loading
 * 
 * @author Nency Sorathiya
 * 
 */

// Load configuration
require_once __DIR__ . '/config.php';

// Get requested page from URL
$page = $_GET['page'] ?? 'home';

// Sanitize page name (remove any path traversal attempts)
$page = basename($page);
$page = str_replace('.php', '', $page);
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

// Define route mappings
$routes = [
    // Public pages
    'home' => 'homepage.php',
    '' => 'homepage.php',
    
    // App pages
    'courses' => 'app/courses.php',
    'about' => 'app/about.php',
    'contact' => 'app/contact.php',
    'certificates' => 'app/certificates.php',
    'certificate' => 'app/certificates.php',
    'pricing' => 'app/pricing.php',
    'live-code' => 'app/live-code.php',
    'lessons' => 'app/lessons.php',
    'quiz' => 'app/quiz.php',
    
    // Auth pages
    'forget-pass-page' => 'app/forget-pass-page.php',
    'forgot-password' => 'app/forgot_password.php',
    'reset-password' => 'app/reset-password.php',
    'logout' => 'app/logout.php',
    
    // Admin pages (protected)
    'admin-panel' => 'app/admin-panel.php',
    'admin' => 'app/admin-panel.php',
    
    // Other pages
    'payment' => 'app/payment.php',
    'payment-success' => 'app/payment_success.php',
    'chatbot' => 'app/chatbot.php',
    'search' => 'app/search_courses.php',
    'profile' => 'app/update_profile.php',
    'terms' => 'app/trem-of-ser.php',
    'trem-of-ser' => 'app/trem-of-ser.php',
];

// Handle special routes
switch ($page) {
    case 'logout':
        // Destroy session and redirect
        session_destroy();
        header('Location: ' . BASE_URL);
        exit;
        
    case 'home':
    case '':
        // Load homepage
        require_once __DIR__ . '/homepage.php';
        exit;
}

// Check if route exists
if (isset($routes[$page])) {
    $filePath = __DIR__ . '/' . $routes[$page];
    
    // Check if file exists
    if (file_exists($filePath)) {
        // Load the requested page
        require_once $filePath;
        exit;
    } else {
        // File not found in routes
        error_log("Router: File not found - $filePath");
    }
}

// 404 - Page Not Found
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | CodeLearn</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
        }
        
        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 60px 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .error-icon {
            font-size: 100px;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        
        h1 {
            font-size: 72px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        h2 {
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        p {
            font-size: 18px;
            margin-bottom: 15px;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .requested-page {
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-size: 16px;
        }
        
        .buttons {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 15px 30px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        a:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        a.secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        a.secondary:hover {
            background: white;
            color: #667eea;
        }
        
        .suggestions {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .suggestions h3 {
            font-size: 20px;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .suggestions ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        
        .suggestions li {
            background: rgba(255, 255, 255, 0.15);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .suggestions a {
            background: none;
            padding: 0;
            color: white;
            text-decoration: underline;
            box-shadow: none;
        }
        
        .suggestions a:hover {
            transform: none;
            opacity: 0.8;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 40px 25px;
            }
            
            h1 {
                font-size: 56px;
            }
            
            h2 {
                font-size: 22px;
            }
            
            p {
                font-size: 16px;
            }
            
            .buttons {
                flex-direction: column;
            }
            
            a {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">🔍</div>
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>Oops! The page you're looking for doesn't exist.</p>
        
        <?php if (!empty($page)): ?>
            <div class="requested-page">
                <i class="fas fa-link"></i> Requested: <strong><?php echo htmlspecialchars($page); ?></strong>
            </div>
        <?php endif; ?>
        
        <div class="buttons">
            <a href="<?php echo BASE_URL; ?>">
                <i class="fas fa-home"></i> Go to Home
            </a>
            <a href="<?php echo BASE_URL; ?>courses" class="secondary">
                <i class="fas fa-book"></i> Browse Courses
            </a>
        </div>
        
        <div class="suggestions">
            <h3>Popular Pages:</h3>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>courses">Courses</a></li>
                <li><a href="<?php echo BASE_URL; ?>about">About</a></li>
                <li><a href="<?php echo BASE_URL; ?>contact">Contact</a></li>
                <li><a href="<?php echo BASE_URL; ?>pricing">Pricing</a></li>
                <li><a href="<?php echo BASE_URL; ?>certificates">Certificates</a></li>
            </ul>
        </div>
    </div>
</body>
</html>