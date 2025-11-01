<?php
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['name'] : '';

// Database configuration - SINGLE PLACE
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codelearn_db";

// Fetch user plan if logged in
$userPlan = 'free'; // default
if ($isLoggedIn && isset($_SESSION['user_id'])) {
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT plan FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $userPlan = $result['plan'];
        }
    } catch(PDOException $e) {
        $userPlan = 'free';
    }
}

// Create mysqli connection for feedback queries
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch recent feedback with user and course information
$feedbackQuery = "
    SELECT 
        f.feedback_id,
        f.rating,
        f.comment,
        f.created_at,
        u.name as user_name,
        c.title as course_name
    FROM feedback f
    JOIN users u ON f.user_id = u.user_id
    JOIN courses c ON f.course_id = c.course_id
    ORDER BY f.created_at DESC
    LIMIT 10
";

$feedbackResult = $conn->query($feedbackQuery);
$feedbacks = [];
if ($feedbackResult && $feedbackResult->num_rows > 0) {
    while($row = $feedbackResult->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - CodeLearn</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Header Styles */
        .header {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: bold;
            color: #0c0682ff;
            text-decoration: none;
        }

        .logo i {
            margin-right: 8px;
            background: #060270ff;
            color: white;
            padding: 8px;
            border-radius: 8px;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            align-items: center;
            gap: 30px;
        }

        .nav-menu li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-menu li a:hover {
            color: #09046bff;
        }

        .search-container {
            position: relative;
            margin: 0 20px;
        }

        .search-box {
            padding: 10px 40px 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            width: 300px;
            outline: none;
            transition: border-color 0.3s;
        }

        .search-box:focus {
            border-color: #08035bff;
        }

        .search-icon, .mic-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            cursor: pointer;
        }

        .mic-icon {
            right: 10px;
            color: #050505ff;
        }

        .mic-active {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: translateY(-50%) scale(1); }
            50% { transform: translateY(-50%) scale(1.1); }
            100% { transform: translateY(-50%) scale(1); }
        }

        .auth-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-primary {
            background: #ebedf0ff;
            color: white;
        }

        .btn-primary:hover {
            background: #f2f2f5ff;
            transform: translateY(-2px);
        }

        .btn-outline {
            border: 2px solid #f1f1f5ff;
            color: #dcdee1ff;
            background: transparent;
        }

        .btn-outline:hover {
            background: #f3f3f8ff;
            color: white;
        }

        /* User Profile Dropdown */
        .user-profile {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            color: #333;
        }

        .user-profile:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .profile-pic {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
            line-height: 1.2;
        }

        .user-email {
            font-size: 12px;
            opacity: 0.8;
            line-height: 1.2;
        }

        .dropdown-arrow {
            margin-left: 8px;
            transition: transform 0.3s ease;
            font-size: 12px;
            opacity: 0.8;
        }

        .user-profile.active .dropdown-arrow {
            transform: rotate(180deg);
        }

        .dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            min-width: 280px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px) scale(0.95);
            transition: all 0.3s ease;
            z-index: 1000;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        .dropdown-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            color: white;
            text-align: center;
        }

        .dropdown-profile-pic {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
            margin: 0 auto 12px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .dropdown-user-name {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .dropdown-user-email {
            font-size: 13px;
            opacity: 0.9;
        }

        .dropdown-menu {
            padding: 8px 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            text-decoration: none;
            color: #333;
            transition: all 0.2s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dropdown-item:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.1), rgba(102, 126, 234, 0.05));
            color: #667eea;
            padding-left: 24px;
        }

        .dropdown-item i {
            width: 20px;
            font-size: 14px;
            margin-right: 12px;
            opacity: 0.7;
        }

        .dropdown-item.logout {
            color: #e74c3c;
        }

        .dropdown-item.logout:hover {
            background: linear-gradient(90deg, rgba(231, 76, 60, 0.1), rgba(231, 76, 60, 0.05));
            color: #c0392b;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: #333;
            margin: 3px 0;
            transition: 0.3s;
        }

        /* About Content Styles */
        .about-content {
            margin-top: 70px;  /* Reduced */
            background: linear-gradient(to bottom, #f0f4ff 0%, #ffffff 100%);
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        header.about-header {
            text-align: center;
            padding: 20px 20px 30px;
            background: linear-gradient(to bottom, #f0f4ff 0%, #ffffff 100%);
        }
        

        .passion-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            border: 1px solid #e5e7eb;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 20px;  /* Change from 30px to 20px */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .passion-badge .heart {
            color: #ef4444;
        }

        h1 {
            font-size: 48px;  /* Reduced from 72px */
            margin-bottom: 20px;
            color: #1f2937;
            font-weight: 800;
            line-height: 1.1;
        }
        .about-header h1 {
            font-size: 40px !important;
            margin-bottom: 15px !important;
            color: #1f2937;
            font-weight: 800;
            line-height: 1.1;
        }

        h1 .brand {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            color: #6b7280;
            font-size: 20px;
            max-width: 900px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .section {
            margin-bottom: 80px;
        }

        .section-title {
            text-align: center;
            font-size: 32px;
            margin-bottom: 20px;
            color: #1f2937;
        }

        .section-description {
            text-align: center;
            color: #6b7280;
            max-width: 800px;
            margin: 0 auto 40px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .stat-card {
            text-align: center;
            padding: 30px;
        }

        .stat-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #6366f1;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #6b7280;
            font-size: 14px;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .value-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .value-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .value-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .value-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #1f2937;
        }

        .value-description {
            color: #6b7280;
            font-size: 14px;
        }

        .journey-timeline {
            max-width: 600px;
            margin: 40px auto 0;
        }

        .timeline-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px 25px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .timeline-item:hover {
            transform: translateX(10px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.2);
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-text {
            color: #1f2937;
            font-size: 15px;
        }

        .cta-section {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #a855f7 100%);
            padding: 80px 20px;
            text-align: center;
            margin-top: 80px;
            position: relative;
            overflow: hidden;
        }

        .cta-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-title {
            font-size: 36px;
            color: white;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .cta-description {
            color: rgba(255, 255, 255, 0.9);
            font-size: 18px;
            margin-bottom: 30px;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: white;
            color: #6366f1;
            padding: 15px 35px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }
        /* Profile Edit Modal */
.profile-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.6);
    backdrop-filter: blur(5px);
    animation: fadeIn 0.3s ease;
}

.profile-modal.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.profile-modal-content {
    background: white;
    margin: 3% auto;
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from { 
        opacity: 0; 
        transform: translateY(-30px) scale(0.9); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
    }
}

.profile-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 25px;
    text-align: center;
    color: white;
    position: relative;
}

.profile-modal-close {
    position: absolute;
    right: 20px;
    top: 20px;
    color: white;
    font-size: 24px;
    cursor: pointer;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.profile-modal-close:hover {
    background: rgba(255,255,255,0.2);
    transform: rotate(90deg);
}

.profile-modal-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 8px;
}

.profile-modal-subtitle {
    font-size: 14px;
    opacity: 0.9;
}

.profile-form {
    padding: 30px;
}

.profile-image-section {
    text-align: center;
    margin-bottom: 30px;
}

.current-profile-pic {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 36px;
    margin: 0 auto 20px;
    border: 4px solid #f0f0f0;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    position: relative;
    overflow: hidden;
}

.profile-image-input {
    display: none;
}

.change-image-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.change-image-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}
/* Login/Signup Modal Styles */
.auth-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    backdrop-filter: blur(3px);
}

.auth-modal-content {
    background: white;
    margin: 8% auto;
    border-radius: 16px;
    width: 90%;
    max-width: 380px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.9); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.auth-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    text-align: center;
    color: white;
    position: relative;
}

.modal-close {
    position: absolute;
    right: 15px;
    top: 15px;
    color: white;
    font-size: 20px;
    cursor: pointer;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.modal-close:hover {
    background: rgba(255,255,255,0.2);
}

.modal-logo {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 5px;
}

.modal-subtitle {
    font-size: 13px;
    opacity: 0.9;
}

.auth-forms {
    padding: 25px;
}

.form-tabs {
    display: flex;
    margin-bottom: 20px;
    background: #f5f5f5;
    border-radius: 6px;
    padding: 3px;
}

.tab-btn {
    flex: 1;
    padding: 8px 12px;
    border: none;
    background: transparent;
    color: #666;
    font-weight: 500;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
}

.tab-btn.active {
    background: white;
    color: #333;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-content {
    display: none;
}

.form-content.active {
    display: block;
}

.divider {
    text-align: center;
    margin: 15px 0;
    position: relative;
    color: #999;
    font-size: 12px;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #eee;
}

.divider span {
    background: white;
    padding: 0 12px;
    position: relative;
}

.auth-forms .form-group {
    margin-bottom: 15px;
}

.auth-forms .form-group input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s;
    outline: none;
    box-sizing: border-box;
}

.auth-forms .form-group input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

.auth-forms .form-group input::placeholder {
    color: #999;
    font-size: 13px;
}

.auth-submit {
    width: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    margin: 15px 0 10px;
}

.auth-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}
/* Desktop Auth Buttons - Separate from nav menu */
.auth-buttons.desktop-only {
    display: flex;
    gap: 15px;
    align-items: center;
}


.forgot-link {
    text-align: center;
    margin-top: 10px;
}

.forgot-link a {
    color: #667eea;
    text-decoration: none;
    font-size: 13px;
}

a.btn.btn-outline {
    color: black;
}
a.btn.btn-primary {
    color: black;
}
.error-message {
    background: #fee;
    color: #c33;
    padding: 8px 12px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 13px;
    display: none;
}

.success-message {
    background: #efe;
    color: #2a7;
    padding: 8px 12px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 13px;
    display: none;
}

@media (max-width: 480px) {
    .auth-modal-content {
        width: 95%;
        margin: 15% auto;
    }
    .auth-forms {
        padding: 20px;
    }
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 600;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s;
    outline: none;
    background: #fafafa;
}

.form-input:focus {
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-buttons {
    display: flex;
    gap: 12px;
    margin-top: 30px;
}

.btn-save {
    flex: 1;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 14px 20px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.btn-cancel {
    flex: 1;
    background: #f8f9fa;
    color: #666;
    border: 2px solid #e0e0e0;
    padding: 14px 20px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-cancel:hover {
    background: #e9ecef;
    border-color: #ccc;
}

.message {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    display: none;
}

.message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.profile-pic-preview {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #f0f0f0;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

@media (max-width: 480px) {
    .profile-modal-content {
        width: 95%;
        margin: 5% auto;
    }
    
    .profile-form {
        padding: 20px;
    }
    
    .form-buttons {
        flex-direction: column;
    }
}   
        /* Testimonials Section Styles */
.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-top: 40px;
}

.testimonial-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 24px;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    gap: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    border-color: #6366f1;
}

.testimonial-header {
    display: flex;
    align-items: center;
    gap: 12px;
}

.student-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 18px;
    flex-shrink: 0;
}

.student-info {
    flex: 1;
    min-width: 0;
}

.student-name {
    font-weight: 600;
    font-size: 16px;
    color: #1f2937;
    margin-bottom: 4px;
}

.course-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #6366f1;
    background: #eef2ff;
    padding: 4px 10px;
    border-radius: 12px;
    font-weight: 500;
}

.course-badge i {
    font-size: 10px;
}

.rating-stars {
    color: #fbbf24;
    font-size: 20px;
    letter-spacing: 2px;
}

.testimonial-text {
    color: #4b5563;
    font-size: 14px;
    line-height: 1.6;
    font-style: italic;
    flex: 1;
}

.testimonial-date {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #9ca3af;
    padding-top: 12px;
    border-top: 1px solid #f3f4f6;
}

.testimonial-date i {
    font-size: 11px;
}

.no-feedback {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: white;
    border: 2px dashed #e5e7eb;
    border-radius: 16px;
}

.no-feedback-icon {
    font-size: 48px;
    margin-bottom: 16px;
}

.no-feedback-title {
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 8px;
}

.no-feedback-text {
    color: #6b7280;
    font-size: 14px;
}
/* Help Modal & General Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 30px;
    border-radius: 15px;
    width: 90%;
    max-width: 500px;
    position: relative;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    right: 20px;
    top: 15px;
}

.close:hover {
    color: #000;
}

.modal h2 {
    margin-bottom: 20px;
    color: #333;
}

.support-options {
    display: grid;
    gap: 15px;
}

.support-option {
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 15px;
}

.support-option:hover {
    border-color: #6c63ff;
    background: #f8f9ff;
}

.support-icon {
    width: 40px;
    height: 40px;
    background: #6c63ff;
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Profile Modal */
.profile-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.6);
    backdrop-filter: blur(5px);
    animation: fadeIn 0.3s ease;
}

.profile-modal.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.profile-modal-content {
    background: white;
    margin: 3% auto;
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from { 
        opacity: 0; 
        transform: translateY(-30px) scale(0.9); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
    }
}

.profile-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 25px;
    text-align: center;
    color: white;
    position: relative;
}

.profile-modal-close {
    position: absolute;
    right: 20px;
    top: 20px;
    color: white;
    font-size: 24px;
    cursor: pointer;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.profile-modal-close:hover {
    background: rgba(255,255,255,0.2);
    transform: rotate(90deg);
}

.profile-modal-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 8px;
}

.profile-modal-subtitle {
    font-size: 14px;
    opacity: 0.9;
}

.profile-form {
    padding: 30px;
}

.profile-image-section {
    text-align: center;
    margin-bottom: 30px;
}

.current-profile-pic {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 36px;
    margin: 0 auto 20px;
    border: 4px solid #f0f0f0;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.form-label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 600;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s;
    outline: none;
    background: #fafafa;
    box-sizing: border-box;
}

.form-input:focus {
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-buttons {
    display: flex;
    gap: 12px;
    margin-top: 30px;
}

.btn-save {
    flex: 1;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 14px 20px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.btn-cancel {
    flex: 1;
    background: #f8f9fa;
    color: #666;
    border: 2px solid #e0e0e0;
    padding: 14px 20px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-cancel:hover {
    background: #e9ecef;
    border-color: #ccc;
}

.message {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    display: none;
}

.message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 480px) {
    .profile-modal-content {
        width: 95%;
        margin: 5% auto;
    }
    
    .profile-form {
        padding: 20px;
    }
    
    .form-buttons {
        flex-direction: column;
    }
}

/* Responsive Design for Testimonials */
@media (max-width: 1024px) {
    .testimonials-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {
    .testimonials-grid {
        grid-template-columns: 1fr;
    }
}

        /* Footer */
        .footer {
            background: #1a1a1a;
            color: white;
            padding: 60px 0 30px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-brand h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .footer-brand p {
            color: #ccc;
            line-height: 1.6;
        }

        .footer-section h4 {
            margin-bottom: 20px;
            color: white;
        }

        .footer-section a {
            display: block;
            color: #ccc;
            text-decoration: none;
            margin-bottom: 10px;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: #6c63ff;
        }

        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 20px;
            text-align: center;
            color: #ccc;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-menu {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 70px);
                background: white;
                flex-direction: column;
                justify-content: start;
                align-items: center;
                padding-top: 50px;
                transition: left 0.3s;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            }

            .nav-menu.active {
                left: 0;
            }

            .hamburger {
                display: flex;
            }

            .hamburger.active span:nth-child(1) {
                transform: rotate(-45deg) translate(-5px, 6px);
            }

            .hamburger.active span:nth-child(2) {
                opacity: 0;
            }

            .hamburger.active span:nth-child(3) {
                transform: rotate(45deg) translate(-5px, -6px);
            }

            .search-container {
                width: 100%;
                margin: 20px 0;
            }

            .search-box {
                width: 100%;
            }

            .auth-buttons {
                flex-direction: column;
                width: 100%;
                padding: 0 20px;
            }

            h1 {
                font-size: 40px;
            }

            .subtitle {
                font-size: 16px;
            }

            .section-title {
                font-size: 24px;
            }

            .stats-grid,
            .values-grid {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .timeline-item {
                flex-direction: column;
                text-align: center;
            }
        }
        * ========== RESPONSIVE STYLES ========== */
@media (max-width: 768px) {
    /* Hide desktop user profile on mobile */
    .desktop-only {
        display: none !important;
    }

    /* Show mobile menu items */
    .mobile-only {
        display: block !important;
    }
    
    /* Show mobile user profile menu */
    .user-profile-mobile {
        display: block !important;
        width: 100%;
        padding: 10px 0;
        border-top: 1px solid #e0e0e0;
        margin-top: 10px;
    }
    
    /* Mobile menu item icons */
    .mobile-only a i {
        width: 20px;
        font-size: 14px;
        margin-right: 10px;
    }
    /* Navigation Container - Mobile Layout */
    .nav-container {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 15px;
        padding: 0 15px;
    }
    
    .logo {
        grid-column: 1;
        font-size: 20px;
    }
    
    .logo i {
        padding: 6px;
        font-size: 16px;
    }
    
    /* Search Container - Center */
    .search-container {
        grid-column: 2;
        margin: 0;
        width: 100%;
        max-width: 300px;
        justify-self: center;
    }
    
    .search-box {
        width: 100%;
        padding: 8px 35px 8px 12px;
        font-size: 14px;
    }
    
    /* Hamburger - Right */
    .hamburger {
        grid-column: 3;
        display: flex;
        z-index: 1001;
    }
    
    .hamburger.active span:nth-child(1) {
        transform: rotate(-45deg) translate(-5px, 6px);
    }
    
    .hamburger.active span:nth-child(2) {
        opacity: 0;
    }
    
    .hamburger.active span:nth-child(3) {
        transform: rotate(45deg) translate(-5px, -6px);
    }
    
    /* Navigation Menu */
    /* Navigation Menu */
    .nav-menu {
        position: fixed;
        top: 70px;
        left: -100%;
        width: 100%;
        height: calc(100vh - 70px);
        background: white;
        flex-direction: column;
        justify-content: start;
        align-items: center;
        padding-top: 0;  /* Already 0 - good */
        transition: left 0.3s ease;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        overflow-y: auto;
        z-index: 999;
        grid-column: 1 / -1;
    }
        
    .nav-menu.active {
        left: 0;
    }
    
    .nav-menu li {
        width: 100%;
        text-align: center;
        margin: 0;  /* Ensure no margin */
        padding: 0; /* Add this */
    }

    .nav-menu li a {
        display: block;
        padding: 10px 20px;  /* Reduce from 12px to 10px */
        width: 100%;
    }
    
    /* Auth Buttons in Mobile Menu */
    /* Auth Buttons in Mobile Menu */
    .auth-buttons {
        flex-direction: column;
        width: 90%;
        padding: 0;
        gap: 10px;
        margin: 15px auto 10px auto;  /* Top aur bottom margin reduce karo */
        border-top: none;  /* Border remove */
        padding-top: 10px;  /* Reduce from 20px */
    }
    .auth-buttons-mobile {
        display: flex !important;
        flex-direction: column;
        gap: 10px;
        padding: 15px 20px;
    }
    
    .auth-buttons .btn {
        width: 100%;
        text-align: center;
    }
    .auth-buttons-mobile .btn {
        width: 100%;
        text-align: center;
    }
    .logout-link {
        color: #e74c3c !important;
    }
    .logout-link:hover {
        background: rgba(231, 76, 60, 0.1) !important;
        color: #c0392b !important;
    }
    
    /* Main Content */
    .container {
        margin-top: 80px;
        padding: 0 15px;
    }
    
    .page-title {
        font-size: 32px;
    }
    
    .page-subtitle {
        font-size: 16px;
        padding: 0 10px;
    }
    
    .contact-section {
        padding: 30px 20px;
        margin-bottom: 40px;
    }
    
    .contact-methods {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .feedback-section {
        padding: 30px 20px;
    }
    
    .feedback-header h3 {
        font-size: 24px;
    }
    
    /* Footer */
    .footer {
        padding: 40px 0 20px;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
        gap: 30px;
        text-align: center;
    }
    
    .footer-brand,
    .footer-section {
        text-align: center;
    }
    
    /* Modals */
    .auth-modal-content,
    .profile-modal-content {
        width: 95%;
        margin: 10% auto;
    }
    
    .auth-forms,
    .profile-form {
        padding: 20px;
    }
}

/* Extra Small Devices - Mobile Portrait */
@media (max-width: 480px) {
    .nav-container {
        padding: 0 10px;
        gap: 10px;
    }
    
    .logo {
        font-size: 18px;
    }
    
    .search-container {
        max-width: 200px;
    }
    
    .search-box {
        padding: 7px 30px 7px 10px;
        font-size: 13px;
    }
    
    .mic-icon {
        right: 8px;
        font-size: 13px;
    }
    
    .hamburger span {
        width: 22px;
        height: 2.5px;
    }
    
    .container {
        padding: 0 10px;
    }
    
    .page-title {
        font-size: 28px;
        line-height: 1.3;
    }
    
    .page-subtitle {
        font-size: 14px;
    }
    
    .contact-title {
        font-size: 24px;
    }
    
    .contact-subtitle {
        font-size: 14px;
    }
    
    .method-title {
        font-size: 16px;
    }
    
    .method-detail {
        font-size: 14px;
    }
    
    .method-description {
        font-size: 13px;
    }
    
    .section-divider h2 {
        font-size: 28px;
    }
    
    .section-divider p {
        font-size: 14px;
    }
    
    .feedback-header h3 {
        font-size: 20px;
    }
    
    .feedback-header p {
        font-size: 14px;
    }
    
    .form-label {
        font-size: 13px;
    }
    
    .form-textarea,
    .form-select {
        font-size: 14px;
        padding: 10px 12px;
    }
    
    .star {
        font-size: 28px;
    }
    
    .submit-button {
        padding: 12px;
        font-size: 15px;
    }
    
    .footer-brand h3 {
        font-size: 1.3rem;
    }
    
    .footer-brand p {
        font-size: 14px;
    }
    
    .footer-section h4 {
        font-size: 16px;
        margin-bottom: 15px;
    }
    
    .footer-section a {
        font-size: 14px;
        margin-bottom: 8px;
    }
    
    .footer-bottom p {
        font-size: 13px;
        padding: 0 10px;
    }
    
    .auth-modal-content {
        width: 95%;
        margin: 15% auto;
    }
    
    .modal-logo {
        font-size: 20px;
    }
    
    .modal-subtitle {
        font-size: 12px;
    }
    
    .auth-forms {
        padding: 18px;
    }
    
    .tab-btn {
        padding: 7px 10px;
        font-size: 13px;
    }
    
    .auth-forms .form-group input {
        padding: 11px 12px;
        font-size: 13px;
    }
    
    .auth-submit {
        padding: 11px;
        font-size: 13px;
    }
    
    .profile-modal-content {
        width: 95%;
        margin: 5% auto;
    }
    
    .profile-modal-title {
        font-size: 20px;
    }
    
    .profile-modal-subtitle {
        font-size: 13px;
    }
    
    .profile-form {
        padding: 20px;
    }
    
    .current-profile-pic {
        width: 80px;
        height: 80px;
        font-size: 28px;
    }
    
    .form-input {
        padding: 12px 14px;
        font-size: 15px;
    }
    
    .btn-save,
    .btn-cancel {
        padding: 12px 16px;
        font-size: 15px;
    }
    
    .form-buttons {
        flex-direction: column;
        gap: 10px;
    }
}
/* Mobile only class */
@media (max-width: 768px) {
    .desktop-only {
        display: none !important;
    }
    
    .mobile-only {
        display: block !important;
    }
}

/* Desktop - hide mobile items */
@media (min-width: 769px) {
    .desktop-only {
        display: flex !important;
    }
    
    .mobile-only {
        display: none !important;
    }
}

/* Tablet Specific */
@media (min-width: 481px) and (max-width: 768px) {
    .search-container {
        max-width: 350px;
    }
    
    .search-box {
        font-size: 15px;
    }
    
    .contact-methods {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .page-title {
        font-size: 36px;
    }
    
    .footer-content {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Large Tablets & Small Desktops */
@media (min-width: 769px) and (max-width: 1024px) {
    .nav-container {
        padding: 0 30px;
    }
    
    .search-box {
        width: 250px;
    }
    
    .contact-methods {
        gap: 25px;
    }
    
    .footer-content {
        gap: 35px;
    }
}

/* Large Screens */
@media (min-width: 769px) {
    .nav-container {
        display: flex;
        justify-content: space-between;
    }
    
    .search-container {
        position: relative;
        margin: 0 20px;
    }
    
    .hamburger {
        display: none;
    }
    
    .nav-menu {
        display: flex;
        flex-direction: row;
        position: static;
        height: auto;
        width: auto;
        background: transparent;
        box-shadow: none;
    }
}
        /* Desktop/Mobile Visibility Controls */
.desktop-only {
    display: flex !important;
}

.mobile-only {
    display: none !important;
}

@media (max-width: 768px) {
    .desktop-only {
        display: none !important;
    }
    
    .mobile-only {
        display: block !important;
    }
    header.about-header {
        padding: 20px 15px 30px;
    }
    
    h1 {
        font-size: 32px;
        margin-bottom: 15px;
    }
    
    .passion-badge {
        margin-bottom: 15px;
        font-size: 13px;
        padding: 8px 16px;
    }
}
/* FORCE OVERRIDE - Add at end */
.about-content .about-header h1 {
    font-size: 40px !important;
    margin-bottom: 15px !important;
}

.about-content header.about-header {
    padding: 20px 20px 30px !important;
}
.message {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    display: none;
}

.message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
    </style>
</head>
<body data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">
    <!-- Header -->
<!-- Header -->
<header class="header">
    <nav class="nav-container">
        <a href="index.php" class="logo">
            <i class="fas fa-code"></i>
            CodeLearn
        </a>
        
        <!-- Search Box -->
        <div class="search-container">
        <input type="text" class="search-box" placeholder="Search courses..." id="searchBox">
        <!-- <i class="fas fa-search search-icon" id="searchIcon" title="Search"></i> -->
        <i class="fas fa-microphone mic-icon" id="micIcon" title="Voice Search"></i>
    </div>
        
        <!-- Hamburger -->
        <div class="hamburger" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <!-- Navigation Menu -->
        <ul class="nav-menu" id="navMenu">
            <li><a href="./courses.php">Courses</a></li>
            <li><a href="./about.php">About</a></li>
            <li><a href="./contact.php">Contact</a></li>
            <li><a href="./certificates.php">Certificate</a></li>
            <li><a href="./pricing.php">Pricing</a></li>
            
            <?php if ($isLoggedIn): ?>
                <!-- Mobile User Menu Items -->
                <li class="mobile-only"><a href="#" onclick="openProfileModal(); event.preventDefault(); return false;"><i class="fas fa-user"></i> Profile</a></li>
                <li class="mobile-only"><a href="pricing.php"><i class="fas fa-crown"></i> Plans & Pricing</a></li>
                <li class="mobile-only"><a href="#" onclick="openModal('helpModal'); event.preventDefault(); return false;"><i class="fas fa-question-circle"></i> Help & Support</a></li>
                <li class="mobile-only"><a href="#" onclick="event.preventDefault(); logout(); return false;" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <?php else: ?>
                <!-- Mobile Auth Buttons -->
                <li class="auth-buttons-mobile mobile-only">
                    <a href="#" onclick="openAuthModal(); return false;" class="btn btn-outline">Sign In</a>
                    <!-- <a href="#" onclick="openAuthModal(); return false;" class="btn btn-primary">Get Started</a> -->
                </li>
            <?php endif; ?>
        </ul>
        
                <!-- Desktop Auth Buttons - Always visible when logged out -->
            <?php if (!$isLoggedIn): ?>
                <div class="auth-buttons desktop-only">
                    <a href="#" onclick="openAuthModal(); return false;" class="btn btn-outline">Sign In</a>
                    <!-- <a href="#" onclick="openAuthModal(); return false;" class="btn btn-primary">Get Started</a> -->
                </div>
            <?php endif; ?>

        <!-- Desktop User Profile -->
       <!-- Desktop User Profile -->
<?php if ($isLoggedIn): ?>
    <div class="user-profile desktop-only" id="userProfileBtn">
        <div class="profile-pic"><?php echo strtoupper(substr($userName, 0, 1)); ?></div>
        <div class="user-info">
            <div class="user-name">
                <?php echo $userName; ?>
                <?php 
                $userPlan = isset($_SESSION['plan']) ? $_SESSION['plan'] : 'free';
                if ($userPlan === 'pro'): ?>
                    <i class="fas fa-crown" style="color: #3B82F6; margin-left: 4px; font-size: 12px;" title="Pro Member"></i>
                <?php elseif ($userPlan === 'team'): ?>
                    <i class="fas fa-star" style="color: #8B5CF6; margin-left: 4px; font-size: 12px;" title="Max Member"></i>
                <?php endif; ?>
            </div>
            <div class="user-email"><?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'user@codelearn.com'; ?></div>
        </div>
        <i class="fas fa-chevron-down dropdown-arrow"></i>
        
        <div class="dropdown" id="userDropdown">
            <div class="dropdown-header">
                <div class="dropdown-profile-pic"><?php echo strtoupper(substr($userName, 0, 1)); ?></div>
                <div class="dropdown-user-name"><?php echo $userName; ?></div>
                <div class="dropdown-user-email"><?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'user@codelearn.com'; ?></div>
                
                <?php
                if ($userPlan === 'pro'): ?>
                    <div style="margin-top: 12px; background: linear-gradient(135deg, #3B82F6, #8B5CF6); color: white; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-crown"></i> Pro Member
                    </div>
                <?php elseif ($userPlan === 'team'): ?>
                    <div style="margin-top: 12px; background: linear-gradient(135deg, #8B5CF6, #A855F7); color: white; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-star"></i> Max Member
                    </div>
                <?php else: ?>
                    <div style="margin-top: 12px; background: #E5E7EB; color: #6B7280; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fas fa-user"></i> Free Plan
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="dropdown-menu">
                <a href="#" onclick="openProfileModal(); event.stopPropagation(); return false;" class="dropdown-item">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
                <a href="pricing.php" class="dropdown-item">
                    <i class="fas fa-crown"></i>
                    <span>Plans & Pricing</span>
                </a>
                <a href="#" onclick="openModal('helpModal'); event.stopPropagation(); return false;" class="dropdown-item">
                            <i class="fas fa-question-circle"></i>
                            <span>Help & Support</span>
                        </a>
                <a href="#" onclick="logout(); event.stopPropagation(); return false;" class="dropdown-item logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- <div class="auth-buttons desktop-only">
        <a href="#" onclick="openAuthModal(); return false;" class="btn btn-outline">Sign In</a>
        <a href="#" onclick="openAuthModal(); return false;" class="btn btn-primary">Get Started</a>
    </div> -->
<?php endif; ?>
    </nav>
</header>
    <!-- About Content -->
    <div class="about-content">
        <div class="container">
            <header class="about-header">
                <div class="passion-badge">
                    <span class="heart">❤️</span>
                    <span>Made with passion for developers</span>
                </div>
                <h1>About <span class="brand">CodeLearn</span></h1>
                <p class="subtitle">We're on a mission to make programming education accessible, engaging, and effective for millions of learners worldwide through AI-powered technology.</p>
            </header>

            <section class="section">
                <h2 class="section-title">Our Mission</h2>
                <p class="section-description">To democratize programming education by combining cutting-edge AI technology with expert-crafted curriculum, making it possible for anyone, anywhere to master coding skills and build a successful career in technology.</p>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">🌍</div>
                        <div class="stat-number">10+</div>
                        <div class="stat-label">Students Worldwide</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">💻</div>
                        <div class="stat-number">15+</div>
                        <div class="stat-label">Programming Languages</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🎯</div>
                        <div class="stat-number">98%</div>
                        <div class="stat-label">Student Success Rate</div>
                    </div>
                </div>
            </section>

            <section class="section">
                <h2 class="section-title">Our Values</h2>
                <p class="section-description">The principles that guide everything we do</p>
                
                <div class="values-grid">
                    <div class="value-card">
                        <div class="value-icon">❤️</div>
                        <div class="value-title">Student-First</div>
                        <div class="value-description">Every decision we make prioritizes the learning experience</div>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">🌐</div>
                        <div class="value-title">Accessibility</div>
                        <div class="value-description">Quality education should be available to everyone, everywhere</div>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">✨</div>
                        <div class="value-title">Innovation</div>
                        <div class="value-description">We constantly push the boundaries of educational technology</div>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">🤝</div>
                        <div class="value-title">Community</div>
                        <div class="value-description">Learning is better together—we foster collaboration</div>
                    </div>
                </div>
            </section>

            <section class="section">
                <h2 class="section-title">Our Journey</h2>
                <p class="section-description">Key milestones in our mission to revolutionize programming education</p>
                
                <div class="journey-timeline">
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-text">CodeLearn founded with a vision to democratize coding education</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-text">Launched AI-powered learning assistant, reached 100K users</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-text">Introduced live code editor, expanded to 10+ programming languages</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-text">Voice command feature launched, 1M+ active learners milestone</div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-text">Industry partnerships established, 5M+ users worldwide</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="cta-section">
                <div class="cta-content">
                    <h2 class="cta-title">Ready to Join Our Community?</h2>
                    <p class="cta-description">Be part of the millions who have transformed their careers with CodeLearn</p>
                    <a href="index.php" class="cta-button">Start Learning Today</a>
                </div>
            </section>
            <!-- Student Testimonials Section -->

            <section class="section">
                <h2 class="section-title">What Our Students Say</h2>
                <p class="section-description">Real feedback from our learning community</p>
                
                <div class="testimonials-grid">
                    <?php if (!empty($feedbacks)): ?>
                        <?php foreach($feedbacks as $feedback): ?>
                            <div class="testimonial-card">
                                <div class="testimonial-header">
                                    <div class="student-avatar">
                                        <?php echo strtoupper(substr($feedback['user_name'], 0, 1)); ?>
                                    </div>
                                    <div class="student-info">
                                        <div class="student-name"><?php echo htmlspecialchars($feedback['user_name']); ?></div>
                                        <div class="course-badge">
                                            <i class="fas fa-book"></i>
                                            <?php echo htmlspecialchars($feedback['course_name']); ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="rating-stars">
                                    <?php 
                                    for($i = 1; $i <= 5; $i++) {
                                        echo $i <= $feedback['rating'] ? '★' : '☆';
                                    }
                                    ?>
                                </div>
                                
                                <div class="testimonial-text">
                                    "<?php echo htmlspecialchars($feedback['comment']); ?>"
                                </div>
                                
                                <div class="testimonial-date">
                                    <i class="fas fa-clock"></i>
                                    <?php echo date('M j, Y', strtotime($feedback['created_at'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-feedback">
                            <div class="no-feedback-icon">💬</div>
                            <div class="no-feedback-title">No feedback yet</div>
                            <div class="no-feedback-text">Be the first to share your experience!</div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
    <!-- Profile Edit Modal -->
<div id="profileModal" class="profile-modal">
    <div class="profile-modal-content">
        <div class="profile-modal-header">
            <span class="profile-modal-close" onclick="closeProfileModal()">&times;</span>
            <div class="profile-modal-title">Edit Profile</div>
            <div class="profile-modal-subtitle">Update your profile information</div>
        </div>
        
        <div class="profile-form">
            <div id="profileMessage" class="message" style="display: none;"></div>
            
            <form id="profileForm" onsubmit="saveProfile(event)">
                <!-- Profile Image Section -->
                <div class="profile-image-section">
                    <div class="current-profile-pic" id="profilePicDisplay">
                        <img id="profileImage" style="display: none;" class="profile-pic-preview">
                        <span id="profileInitials"><?php echo strtoupper(substr($userName, 0, 1)); ?></span>
                    </div>
                    <input type="file" id="profileImageInput" class="profile-image-input" accept="image/*" onchange="previewImage(event)">
                    <!-- <button type="button" class="change-image-btn" onclick="document.getElementById('profileImageInput').click()">
                        <i class="fas fa-camera"></i>
                    </button> -->
                </div>

                <!-- Name Input -->
                <div class="form-group">
                    <label class="form-label" for="profileName">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" id="profileName" class="form-input" value="<?php echo $userName; ?>" required minlength="2" maxlength="50">
                </div>

                <!-- Email Input (Read-only) -->
                <div class="form-group">
                    <label class="form-label" for="profileEmail">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="profileEmail" class="form-input" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'user@codelearn.com'; ?>" readonly style="background: #f5f5f5; cursor: not-allowed;">
                    <small style="color: #666; font-size: 12px; margin-top: 4px; display: block;">Email cannot be changed</small>
                </div>

                <!-- Action Buttons -->
                <div class="form-buttons">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <button type="button" class="btn-cancel" onclick="closeProfileModal()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 <!-- Help Modal -->
    <div id="helpModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('helpModal')">&times;</span>
            <h2>Help & Support</h2>
            <div class="support-options">
                <div class="support-option" onclick="redirectToAuth()">
                    <div class="support-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div>
                        <h4>Live Chat</h4>
                        <p>Get instant help from our support team</p>
                    </div>
                </div>
                
                <div class="support-option" onclick="window.open('mailto:support@codelearn.com')">
                    <div class="support-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h4>Email Support</h4>
                        <p>Send us an email at support@codelearn.com</p>
                    </div>
                </div>
                
                <div class="support-option" onclick="redirectToAuth()">
                    <div class="support-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div>
                        <h4>FAQ</h4>
                        <p>Find answers to common questions</p>
                    </div>
                </div>
                
                <div class="support-option" onclick="redirectToAuth()">
                    <div class="support-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <h4>Documentation</h4>
                        <p>Browse our comprehensive guides</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Login/Signup Modal -->
<div id="authModal" class="auth-modal">
    <div class="auth-modal-content">
        <div class="auth-modal-header">
            <span class="modal-close" onclick="closeAuthModal()">&times;</span>
            <div class="modal-logo">CodeLearn</div>
            <div class="modal-subtitle">Start your coding journey</div>
        </div>
        
        <div class="auth-forms">
            <div class="form-tabs">
                <button class="tab-btn active" onclick="showAuthForm('login')">Sign In</button>
                <button class="tab-btn" onclick="showAuthForm('signup')">Sign Up</button>
            </div>
            
            <div id="errorMessage" class="error-message"></div>
            <div id="successMessage" class="success-message"></div>
            
            <!-- Login Form -->
            <div id="loginForm" class="form-content active">
                <div class="divider"><span>or</span></div>
                
                <form onsubmit="handleAuth(event, 'login')">
                    <div class="form-group">
                        <input type="email" id="loginEmail" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <input type="password" id="loginPassword" name="password" placeholder="Password" required>
                        <i class="fas fa-eye" id="loginPasswordToggle" 
                        onclick="togglePasswordVisibility('loginPassword', 'loginPasswordToggle')" 
                        style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #999; font-size: 14px;"></i>
                    </div>
                    <button type="submit" class="auth-submit">Sign In</button>
                </form>
                
                <div class="forgot-link">
                    <a href="./forget-pass-page.php">Forgot Password?</a>
                </div>
            </div>
            
            <!-- Signup Form -->
            <div id="signupForm" class="form-content">
                <div class="divider"><span>or</span></div>
                
                <form onsubmit="handleAuth(event, 'signup')">
                    <div class="form-group">
                        <input type="text" id="signupName" name="name" placeholder="Full Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="signupEmail" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <input type="password" id="signupPassword" name="password" placeholder="Password" required minlength="6">
                        <i class="fas fa-eye" id="signupPasswordToggle" 
                        onclick="togglePasswordVisibility('signupPassword', 'signupPasswordToggle')" 
                        style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #999; font-size: 14px;"></i>
                    </div>
                    
                    <button type="submit" class="auth-submit">Sign Up</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3>CodeLearn</h3>
                    <p>Empowering developers worldwide with AI-powered learning experiences.</p>
                </div>
                
                <div class="footer-section">
                    <h4>Courses</h4>
                    <a href="courses.php">Python</a>
                    <a href="courses.php">JavaScript</a>
                    <a href="courses.php">React</a>
                    <a href="courses.php">Node.js</a>
                </div>
                
                <div class="footer-section">
                    <h4>Company</h4>
                    <a href="./about.php">About</a>
                    <a href="./contact.php">Contact</a>
                </div>
                
                <div class="footer-section">
                    <h4>Support</h4>
                    <a href="#">Help Center</a>
                    <a href="./trem-of-ser.php">Terms of Service</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 CodeLearn. All rights reserved. Made with  for developers worldwide.</p>
            </div>
        </div>
    </footer>
    

    <script>
    // Mobile menu toggle
function toggleMenu() {
    const navMenu = document.getElementById('navMenu');
    const hamburger = document.querySelector('.hamburger');
    navMenu.classList.toggle('active');
    hamburger.classList.toggle('active');
    
    // Prevent body scroll when menu is open on mobile
    if (navMenu.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = 'auto';
    }
}

// User dropdown toggle
function toggleDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('userDropdown');
    const userProfile = document.querySelector('.user-profile');
    
    dropdown.classList.toggle('active');
    userProfile.classList.toggle('active');
}

// Close dropdown when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    // Close mobile menu when clicking on a link
    const navLinks = document.querySelectorAll('.nav-menu li a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                const navMenu = document.getElementById('navMenu');
                const hamburger = document.querySelector('.hamburger');
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    });

    // User profile dropdown setup
    const userProfile = document.querySelector('.user-profile');
    if (userProfile) {
        userProfile.addEventListener('click', function(event) {
            event.stopPropagation();
            toggleDropdown(event);
        });
    }

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdown');
        const userProfile = document.querySelector('.user-profile');
        
        if (dropdown && userProfile) {
            if (!userProfile.contains(event.target)) {
                dropdown.classList.remove('active');
                userProfile.classList.remove('active');
            }
        }
    });

    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
});

// Handle window resize
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        // Close mobile menu if window is resized to desktop
        if (window.innerWidth > 768) {
            const navMenu = document.getElementById('navMenu');
            const hamburger = document.querySelector('.hamburger');
            const dropdown = document.getElementById('userDropdown');
            
            if (navMenu) {
                navMenu.classList.remove('active');
            }
            if (hamburger) {
                hamburger.classList.remove('active');
            }
            if (dropdown) {
                dropdown.classList.remove('active');
            }
            document.body.style.overflow = 'auto';
        }
    }, 250);
});

// Logout function
function logout() {
    showUserLogoutConfirmation('Confirm Logout', 'Are you sure you want to logout?');
}

function showUserLogoutConfirmation(title, message) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(8px);
        display: flex; align-items: center; justify-content: center;
        z-index: 10000; opacity: 0; transition: all 0.3s ease;
    `;

    const modalContent = document.createElement('div');
    modalContent.innerHTML = `
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">👋</div>
            <div>
                <h3 style="margin: 0; color: #1f2937; font-size: 20px; font-weight: 700;">Confirm Logout ?</h3>
                <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 14px;">Thank you for learning with CodeLearn.</p>
            </div>
        </div>
        <div style="display: flex; gap: 12px;">
            <button id="cancelBtn" style="flex: 1; padding: 12px; border: 1px solid #e5e7eb; background: #f9fafb; color: #374151; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease;">Stay</button>
            <button id="confirmBtn" style="flex: 1; padding: 12px; border: none; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease;">Logout</button>
        </div>
    `;
    modalContent.style.cssText = `
        background: white; padding: 24px; border-radius: 16px; max-width: 360px; width: 90%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: translateY(20px) scale(0.95); transition: all 0.3s ease;
    `;

    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';

    document.getElementById('cancelBtn').onclick = function() {
        closeModal();
    };

    document.getElementById('confirmBtn').onclick = function() {
        this.innerHTML = '<div style="width: 16px; height: 16px; border: 2px solid white; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>';
        this.disabled = true;
        
        fetch('logout.php', { method: 'POST' })
        .then(() => {
            closeModal();
            showLogoutToast();
            setTimeout(() => window.location.href = 'index.php', 2000);
        })
        .catch(() => {
            window.location.href = 'logout.php';
        });
    };

    function closeModal() {
        modal.style.opacity = '0';
        modalContent.style.transform = 'translateY(20px) scale(0.95)';
        setTimeout(() => {
            if (document.body.contains(modal)) {
                document.body.removeChild(modal);
            }
            document.body.style.overflow = 'auto';
        }, 300);
    }

    modal.onclick = function(e) {
        if (e.target === modal) closeModal();
    };

    if (!document.getElementById('logout-spinner-style')) {
        const style = document.createElement('style');
        style.id = 'logout-spinner-style';
        style.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
        document.head.appendChild(style);
    }

    setTimeout(() => {
        modal.style.opacity = '1';
        modalContent.style.transform = 'translateY(0) scale(1)';
    }, 10);
}

function showLogoutToast() {
    const toast = document.createElement('div');
    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">✓</div>
            <div>
                <div style="font-weight: 700; font-size: 15px; color: #111827;">Logout successful!</div>
                <div style="color: #6b7280; font-size: 13px; margin-top: 2px;">Thanks for visiting CodeLearn</div>
            </div>
        </div>
    `;
    toast.style.cssText = `
        position: fixed; top: 24px; right: 24px; background: white; 
        padding: 16px 20px; border-radius: 12px; 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 10001; transform: translateX(400px); transition: all 0.4s ease;
        border: 1px solid #f3f4f6; min-width: 280px;
    `;

    document.body.appendChild(toast);
    
    setTimeout(() => toast.style.transform = 'translateX(0)', 100);
    setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 400);
    }, 1800);
}

// ==================== COURSE SEARCH FUNCTIONALITY ====================
async function searchCourse(query) {
    if (!query || query.trim() === '') {
        showSearchToast('Please enter a search term', 'error');
        return;
    }

    const searchBox = document.getElementById('searchBox');
    const originalPlaceholder = searchBox.placeholder;
    searchBox.placeholder = 'Searching...';
    searchBox.disabled = true;

    try {
        const formData = new FormData();
        formData.append('query', query);

        const response = await fetch('search_courses.php', {
            method: 'POST',
            body: formData
        });

        // Check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Get response text first for debugging
        const responseText = await response.text();
        console.log('Response:', responseText); // Debug log

        // Try to parse JSON
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            console.error('JSON parse error:', e);
            console.error('Response text:', responseText);
            throw new Error('Invalid JSON response from server');
        }

        if (result.success) {
            showSearchToast(`Found: ${result.course_title}`, 'success');
            
            // Check if user is logged in (you can pass this from PHP)
            const isLoggedIn = document.body.dataset.loggedIn === 'true';
            
            if (!isLoggedIn) {
                setTimeout(() => {
                    // Call your auth modal function
                    if (typeof openAuthModal === 'function') {
                        openAuthModal();
                    } else {
                        window.location.href = result.redirect_url;
                    }
                }, 1500);
            } else {
                setTimeout(() => {
                    window.location.href = result.redirect_url;
                }, 1500);
            }
        } else {
            let message = result.message || 'Search failed';
            if (result.suggestions && result.suggestions.length > 0) {
                message += '\n\nDid you mean: ' + result.suggestions.join(', ') + '?';
            }
            showSearchToast(message, 'error');
        }
    } catch (error) {
        console.error('Search error:', error);
        showSearchToast('Search failed: ' + error.message, 'error');
    } finally {
        searchBox.placeholder = originalPlaceholder;
        searchBox.disabled = false;
        searchBox.focus();
    }
}

function showSearchToast(message, type) {
    // Remove any existing toasts first
    const existingToasts = document.querySelectorAll('.search-toast');
    existingToasts.forEach(toast => toast.remove());

    const toast = document.createElement('div');
    toast.className = 'search-toast';
    const icon = type === 'success' ? '✓' : '⚠️';
    const bgColor = type === 'success' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #ef4444, #dc2626)';
    
    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; background: ${bgColor}; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; flex-shrink: 0;">${icon}</div>
            <div style="flex: 1;">
                <div style="font-weight: 700; font-size: 15px; color: #111827; white-space: pre-line; word-break: break-word;">${message}</div>
            </div>
        </div>
    `;
    toast.style.cssText = `
        position: fixed; top: 90px; right: 24px; background: white; 
        padding: 16px 20px; border-radius: 12px; 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 10001; transform: translateX(400px); transition: all 0.4s ease;
        border: 1px solid #f3f4f6; min-width: 300px; max-width: 400px;
    `;

    document.body.appendChild(toast);
    
    setTimeout(() => toast.style.transform = 'translateX(0)', 100);
    setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 400);
    }, 4000);
}

// ==================== VOICE SEARCH ====================
document.addEventListener('DOMContentLoaded', function() {
    const micIcon = document.getElementById('micIcon');
    const searchBox = document.getElementById('searchBox');
    const searchIcon = document.getElementById('searchIcon');
    
    if (micIcon && searchBox) {
        // Voice Search Setup
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'en-US';

            micIcon.addEventListener('click', function() {
                micIcon.classList.add('mic-active');
                searchBox.placeholder = '🎤 Listening...';
                try {
                    recognition.start();
                } catch (e) {
                    console.error('Recognition start error:', e);
                    searchBox.placeholder = 'Search courses...';
                    micIcon.classList.remove('mic-active');
                }
            });

            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                searchBox.value = transcript;
                searchBox.placeholder = 'Search courses...';
                micIcon.classList.remove('mic-active');
                
                // Automatically search after voice input
                searchCourse(transcript);
            };

            recognition.onerror = function(event) {
                console.log('Speech recognition error: ' + event.error);
                searchBox.placeholder = 'Search courses...';
                micIcon.classList.remove('mic-active');
                
                if (event.error === 'no-speech') {
                    showSearchToast('No speech detected. Please try again.', 'error');
                } else if (event.error === 'not-allowed') {
                    showSearchToast('Microphone access denied. Please enable it.', 'error');
                } else {
                    showSearchToast('Voice recognition error: ' + event.error, 'error');
                }
            };

            recognition.onend = function() {
                searchBox.placeholder = 'Search courses...';
                micIcon.classList.remove('mic-active');
            };
        } else {
            micIcon.addEventListener('click', function() {
                showSearchToast('Voice search not supported in your browser', 'error');
            });
        }

        // Text search on Enter key
        searchBox.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = searchBox.value.trim();
                if (searchTerm) {
                    searchCourse(searchTerm);
                }
            }
        });

        // Add search icon click handler
        if (searchIcon) {
            searchIcon.addEventListener('click', function() {
                const searchTerm = searchBox.value.trim();
                if (searchTerm) {
                    searchCourse(searchTerm);
                } else {
                    showSearchToast('Please enter a search term', 'error');
                }
            });
        }
    }
});
// Profile Modal Functions
function openProfileModal() {
    document.getElementById('profileModal').classList.add('active');
    document.body.style.overflow = 'hidden';
    clearProfileMessage();
}

function closeProfileModal() {
    document.getElementById('profileModal').classList.remove('active');
    document.body.style.overflow = 'auto';
    clearProfileMessage();
}
function showProfileMessage(message, type) {
    const messageDiv = document.getElementById('profileMessage');
    messageDiv.textContent = message;
    messageDiv.className = 'message ' + type;
    messageDiv.style.display = 'block';
}

function clearProfileMessage() {
    const messageDiv = document.getElementById('profileMessage');
    if (messageDiv) {
        messageDiv.style.display = 'none';
        messageDiv.textContent = '';
        messageDiv.className = 'message';
    }
}
// // Profile Modal Functions
// function openProfileModal() {
//     document.getElementById('profileModal').classList.add('active');
//     document.body.style.overflow = 'hidden';
//     clearProfileMessage();
// }

// function closeProfileModal() {
//     document.getElementById('profileModal').classList.remove('active');
//     document.body.style.overflow = 'auto';
//     clearProfileMessage();
// }

function showProfileMessage(message, type) {
    const messageDiv = document.getElementById('profileMessage');
    messageDiv.textContent = message;
    messageDiv.className = 'message ' + type;
    messageDiv.style.display = 'block';
}

function clearProfileMessage() {
    const messageDiv = document.getElementById('profileMessage');
    if (messageDiv) {
        messageDiv.style.display = 'none';
        messageDiv.textContent = '';
        messageDiv.className = 'message';
    }
}

async function saveProfile(event) {
    event.preventDefault();
    
    const name = document.getElementById('profileName').value.trim();
    
    if (!name) {
        showProfileMessage('Please enter your name', 'error');
        return;
    }

    if (name.length < 2) {
        showProfileMessage('Name must be at least 2 characters', 'error');
        return;
    }

    const saveBtn = event.target.querySelector('.btn-save');
    const originalHTML = saveBtn.innerHTML;
    
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    try {
        const formData = new FormData();
        formData.append('action', 'update_profile');
        formData.append('name', name);
        
        const response = await fetch('update_profile.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showProfileMessage('✓ Profile updated successfully!', 'success');
            
            // Update displayed name
            document.querySelectorAll('.user-name, .dropdown-user-name').forEach(el => {
                el.textContent = name;
            });
            
            // Update initials
            const initial = name.charAt(0).toUpperCase();
            document.querySelectorAll('.profile-pic, .dropdown-profile-pic, #profileInitials').forEach(el => {
                el.textContent = initial;
            });
            
            setTimeout(() => {
                closeProfileModal();
                location.reload();
            }, 2000);
        } else {
            showProfileMessage(result.message || 'Failed to update profile', 'error');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showProfileMessage('Network error. Please try again.', 'error');
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalHTML;
    }
}


async function saveProfile(event) {
    event.preventDefault();
    
    const name = document.getElementById('profileName').value.trim();
    
    if (!name) {
        showProfileMessage('Please enter your name', 'error');
        return;
    }

    if (name.length < 2) {
        showProfileMessage('Name must be at least 2 characters', 'error');
        return;
    }

    const saveBtn = event.target.querySelector('.btn-save');
    const originalHTML = saveBtn.innerHTML;
    
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    try {
        const formData = new FormData();
        formData.append('action', 'update_profile');
        formData.append('name', name);
        
        const response = await fetch('update_profile.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showProfileMessage('✓ Profile updated successfully!', 'success');
            
            // Update displayed name
            document.querySelectorAll('.user-name, .dropdown-user-name').forEach(el => {
                el.textContent = name;
            });
            
            // Update initials
            const initial = name.charAt(0).toUpperCase();
            document.querySelectorAll('.profile-pic, .dropdown-profile-pic, #profileInitials').forEach(el => {
                el.textContent = initial;
            });
            
            setTimeout(() => {
                closeProfileModal();
                location.reload();
            }, 2000);
        } else {
            showProfileMessage(result.message || 'Failed to update profile', 'error');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showProfileMessage('Network error. Please try again.', 'error');
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalHTML;
    }
}
// Password visibility toggle
function togglePasswordVisibility(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = document.getElementById(iconId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
    
    const authModal = document.getElementById('authModal');
    if (event.target === authModal) {
        closeAuthModal();
    }
    
    const profileModal = document.getElementById('profileModal');
    if (event.target === profileModal) {
        closeProfileModal();
    }
});
// Help Modal Functions
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Authentication Modal Functions
function openAuthModal() {
    document.getElementById('authModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeAuthModal() {
    document.getElementById('authModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    clearMessages();
}

function showAuthForm(formType) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    document.querySelectorAll('.form-content').forEach(form => form.classList.remove('active'));
    document.getElementById(formType + 'Form').classList.add('active');
    
    clearMessages();
}

function showMessage(message, type) {
    clearMessages();
    document.getElementById(type + 'Message').textContent = message;
    document.getElementById(type + 'Message').style.display = 'block';
}

function clearMessages() {
    document.getElementById('errorMessage').style.display = 'none';
    document.getElementById('successMessage').style.display = 'none';
}

async function handleAuth(event, action) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    formData.append('action', action);
    
    const submitBtn = form.querySelector('.auth-submit');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.textContent = action === 'login' ? 'Signing In...' : 'Signing Up...';
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            setTimeout(() => {
                window.location.href = result.redirect || 'about.php';
            }, 1500);
        } else {
            showMessage(result.message, 'error');
        }
        
    } catch (error) {
        showMessage('Network error. Please try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

function togglePasswordVisibility(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = document.getElementById(iconId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Escape key to close modals
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const dropdown = document.getElementById('userDropdown');
        const navMenu = document.getElementById('navMenu');
        const hamburger = document.querySelector('.hamburger');
        
        if (dropdown && dropdown.classList.contains('active')) {
            dropdown.classList.remove('active');
            document.querySelector('.user-profile')?.classList.remove('active');
        }
        
        if (navMenu && navMenu.classList.contains('active')) {
            navMenu.classList.remove('active');
            hamburger?.classList.remove('active');
        }
        
        document.body.style.overflow = 'auto';
    }
});
    </script>
</body>
</html>