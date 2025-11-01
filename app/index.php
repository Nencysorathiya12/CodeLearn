<!-- index.php -->


<?php
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeLearn - Code the Future</title>
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

        .search-icon {
            right: 35px;
        }

        .mic-icon {
            right: 10px;
            color: #050505ff;
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
            background: #f2f2f5ff;
            color: white;
        }

        .btn-primary:hover {
            background: #f3f3f4ff;
            transform: translateY(-2px);
        }

        .btn-outline {
            border: 2px solid #f3f2f8ff;
            color: #f9f9feff;
            background: transparent;
        }

        .btn-outline:hover {
            background: #f8f8faff;
            color: white;
        }

        /* User Profile Dropdown Styles */
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

/* Dropdown Menu */
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
    position: relative;
}

.dropdown-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
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
    position: relative;
}

.dropdown-item:hover {
    background: linear-gradient(90deg, rgba(102, 126, 234, 0.1), rgba(102, 126, 234, 0.05));
    color: #667eea;
    padding-left: 24px;
}

.dropdown-item:last-child {
    border-bottom: none;
}

.dropdown-item i {
    width: 20px;
    font-size: 14px;
    margin-right: 12px;
    opacity: 0.7;
}

.dropdown-item:hover i {
    opacity: 1;
    transform: scale(1.1);
}

.dropdown-item.logout {
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    margin-top: 4px;
    color: #e74c3c;
}

.dropdown-item.logout:hover {
    background: linear-gradient(90deg, rgba(231, 76, 60, 0.1), rgba(231, 76, 60, 0.05));
    color: #c0392b;
}
        /*humburger menu */
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

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #06196cff 0%, #3a056fff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding-top: 80px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.05)" points="0,1000 1000,800 1000,1000"/></svg>');
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
            padding: 0 20px;
            position: relative;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-content h1 .gradient-text {
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .hero-stats {
            display: flex;
            gap: 40px;
            margin: 40px 0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #4ecdc4;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }

        .btn-large {
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 8px;
        }

        .btn-free {
            background: #032d74cd;
            color: white;
            border: none;
        }

        .btn-free:hover {
            transform: translateY(-3px);
        }

        .btn-demo {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-demo:hover {
            background: white;
            color: #667eea;
        }

        .code-editor {
            background: #1e1e1e;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
        }

        .editor-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .traffic-lights {
            display: flex;
            gap: 8px;
        }

        .light {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .red { background: #ff5f57; }
        .yellow { background: #ffbd2e; }
        .green { background: #28ca42; }

        .editor-title {
            color: #fff;
            font-size: 14px;
        }

        .live-indicator {
            background: #4ecdc4;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
        }

        .code-content {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.6;
        }

        .code-line {
            display: flex;
            margin: 5px 0;
        }

        .line-number {
            color: #666;
            width: 30px;
            text-align: right;
            margin-right: 20px;
        }

        .keyword { color: #ff79c6; }
        .function { color: #8be9fd; }
        .string { color: #f1fa8c; }
        .comment { color: #6272a4; }

        .output-section {
            background: #2d2d2d;
            margin-top: 15px;
            padding: 15px;
            border-radius: 8px;
        }

        .output-header {
            color: #4ecdc4;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .output-content {
            color: #50fa7b;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }

        /* Certificate Section */
        .certificate-section {
            padding: 100px 0;
            background: #f8f9ee;
        }

        .certificate-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            text-align: center;
        }

        .certificate-header h2 {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #333;
        }

        .certificate-header p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 50px;
        }

        .certificate-box {
            background: white;
            border: 3px solid #e8f5e8;
            border-radius: 20px;
            padding: 60px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            position: relative;
        }

        .certificate-features {
            text-align: left;
        }

        .certificate-features h3 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .certificate-features h3 i {
            color: #ffd700;
            font-size: 2.2rem;
        }

        .feature-list {
            list-style: none;
            margin-bottom: 30px;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 1rem;
            color: #555;
        }

        .feature-list li i {
            color: #28a745;
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .view-sample-btn {
            background: #ffa500;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .view-sample-btn:hover {
            background: #ff8c00;
            transform: translateY(-2px);
        }

        .certificate-preview {
            text-align: center;
        }

        .certificate-card {
            background: linear-gradient(135deg, #fff9c4, #f4e79d);
            border: 2px solid #ddd;
            border-radius: 15px;
            padding: 40px 30px;
            position: relative;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .certificate-card::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 1px solid #d4af37;
            border-radius: 10px;
            pointer-events: none;
        }

        .cert-trophy {
            font-size: 3rem;
            color: #d4af37;
            margin-bottom: 20px;
        }

        .cert-title {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .cert-course {
            font-size: 1.5rem;
            color: #555;
            margin-bottom: 30px;
            font-weight: 500;
        }

        .cert-recipient {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .cert-score {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
        }

        .cert-verification {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #888;
            font-size: 0.8rem;
        }

        /* Code Editor Section */
        .code-editor-section {
            padding: 100px 0;
            background: #1a1d29;
            color: white;
        }

        .code-editor-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            text-align: center;
        }

        .code-editor-header h2 {
            font-size: 3rem;
            margin-bottom: 20px;
            color: white;
        }

        .code-editor-header p {
            font-size: 1.1rem;
            color: #ccc;
            margin-bottom: 50px;
        }

        .editor-demo {
            background: #2d3748;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            margin-bottom: 50px;
        }

        .demo-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .demo-title {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .status-indicators {
            display: flex;
            gap: 15px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .python-active {
            background: #2ecc71;
            color: white;
        }

        .running {
            background: #3498db;
            color: white;
        }

        .editor-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: flex-start;
        }

        .code-panel {
            background: #1a1a1a;
            border-radius: 10px;
            padding: 25px;
            text-align: left;
        }

        .output-panel {
            background: #0f1419;
            border-radius: 10px;
            padding: 25px;
            text-align: left;
        }

        .panel-header {
            color: #fff;
            font-size: 0.9rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .demo-code {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.8;
            color: #fff;
        }

        .demo-output {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.8;
        }

        .output-line {
            margin: 8px 0;
        }

        .output-line.success {
            color: #2ecc71;
        }

        .output-line.result {
            color: #f1c40f;
        }

        .execution-info {
            color: #27ae60;
            font-size: 0.85rem;
            margin-top: 15px;
        }

        .language-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .lang-tab {
            padding: 8px 16px;
            background: #4a5568;
            color: #cbd5e0;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .lang-tab:hover, .lang-tab.active {
            background: #6c63ff;
            color: white;
        }

        .editor-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .editor-feature {
            background: #2d3748;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            transition: transform 0.3s;
        }

        .editor-feature:hover {
            transform: translateY(-5px);
        }

        .feature-icon-large {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .editor-feature h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: white;
        }

        .editor-feature p {
            color: #a0aec0;
            line-height: 1.5;
        }

        /* Features Section */
        .features {
            padding: 100px 0;
            background: #f8f9fa;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            text-align: center;
        }

        .features-header {
            margin-bottom: 60px;
        }

        .features-header h2 {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #333;
        }

        .features-header p {
            font-size: 1.1rem;
            color: #666;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .feature-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: linear-gradient(45deg, #6c63ff, #4ecdc4);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Courses Section */
        .courses {
            padding: 100px 0;
            max-width: 1200px;
            margin: 0 auto;
        }

        .courses-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .courses-header h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #333;
            font-weight: 600;
        }

        .courses-header p {
            font-size: 1.1rem;
            color: #666;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            padding: 0 20px;
        }

        .course-card {
            background: white;
            border-radius: 20px;
            padding: 30px 25px 25px 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid #f0f0f0;
            position: relative;
        }

        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        .course-meta {
            position: absolute;
            top: 25px;
            right: 25px;
            color: #2196F3;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .pro-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #FF6B35;
            color: white;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .course-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Python Course */
        .course-card.python .course-icon {
            background: #E8F5E8;
            color: #2E7D32;
        }

        /* Python AI Course */
        .course-card.python-ai .course-icon {
            background: #F3E5F5;
            color: #7B1FA2;
        }

        /* JavaScript Course */
        .course-card.javascript .course-icon {
            background: #FFF3E0;
            color: #F57C00;
        }

        /* React Course */
        .course-card.react .course-icon {
            background: #E3F2FD;
            color: #1976D2;
        }

        /* Node.js Course */
        .course-card.nodejs .course-icon {
            background: #E8F5E8;
            color: #388E3C;
        }

        /* Java Course */
        .course-card.java .course-icon {
            background: #FFEBEE;
            color: #D32F2F;
        }

        .course-title {
            font-size: 2rem;
            margin-bottom: 12px;
            font-weight: 600;
            color: #333;
        }

        .course-description {
            font-size: 1rem;
            line-height: 1.5;
            color: #666;
            margin-bottom: 25px;
        }

        .course-stats {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
            font-size: 0.9rem;
            color: #666;
            flex-wrap: wrap;
            gap: 15px;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .stars {
            color: #FFD700;
            font-weight: 600;
        }

        .course-info {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #666;
            font-size: 0.9rem;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .info-item i {
            font-size: 0.85rem;
            opacity: 0.7;
            min-width: 14px;
            flex-shrink: 0;
        }

        .course-button {
            width: 100%;
            padding: 14px 20px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-transform: none;
        }

        .course-card.python {
            background: rgba(220, 248, 221, 1);
        }
        .course-card.python-ai {
           background: rgba(255, 221, 250, 1);
        }
        .course-card.javascript {
            background:rgba(249, 231, 181, 1) ;
        }
        .course-card.react {
              background: rgba(201, 228, 250, 1) ;
        }
        .course-card.nodejs {
            background: rgba(207, 248, 211, 1) ;
        }
        .course-card.java {
           background: rgba(243, 218, 218, 1) ;
        }

        /* Python Course Button */
        .course-card.python .course-button {
            background: linear-gradient(45deg, #8bf58eff, #a6f6aaff);
            color: white;
        }

        /* Python AI Course Button */
        .course-card.python-ai .course-button {
            background: linear-gradient(45deg, #ed9ffbff, #BA68C8);
            color: white;
        }

        /* JavaScript Course Button */
        .course-card.javascript .course-button {
            background: linear-gradient(45deg, #fcd79fff, #FFB74D);
            color: white;
        }

        /* React Course Button */
        .course-card.react .course-button {
            background: linear-gradient(45deg, #c0ddf5ff, #64B5F6);
            color: white;
        }

        /* Node.js Course Button */
        .course-card.nodejs .course-button {
            background: linear-gradient(45deg, #8df990ff, #66BB6A);
            color: white;
        }

        /* Java Course Button */
        .course-card.java .course-button {
            background: linear-gradient(45deg, #f95f54ff, #ef908dff);
            color: white;
        }

        .course-button:hover {
            transform: translateY(-2vh);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .course-button i {
            font-size: 0.9rem;
        }
        /* Password toggle icon hover effect */
        .form-group i.fa-eye,
        .form-group i.fa-eye-slash {
            transition: all 0.2s ease;
        }

        .form-group i.fa-eye:hover,
        .form-group i.fa-eye-slash:hover {
            color: #667eea !important;
            transform: translateY(-50%) scale(1.1);
        }

        .view-all-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 15px 30px;
            background: linear-gradient(45deg, #6c63ff, #4ecdc4);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-top: 20vh;
        }

        .view-all-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(12, 6, 135, 0.9);
        }
        
        /* CTA Section */
        .cta {
            padding: 100px 0;
            background: linear-gradient(135deg, #051c81e3 0%, #5606a7ff 100%);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shine 3s infinite;
            pointer-events: none;
        }
        section.cta {
             margin-top: 15vh;
        }

        .cta::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><circle fill="rgba(255,255,255,0.05)" cx="100" cy="200" r="120"/><circle fill="rgba(255,255,255,0.03)" cx="800" cy="400" r="180"/><circle fill="rgba(255,255,255,0.04)" cx="300" cy="700" r="150"/><circle fill="rgba(255,255,255,0.02)" cx="900" cy="100" r="100"/></svg>');
            pointer-events: none;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .cta-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .cta h2 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .cta p {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
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
        footer.footer {
            margin-top: 20vh;
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

        /* Modal Styles */
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
        /* Login/Signup Modal Styles */
.auth-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.6);
    backdrop-filter: blur(5px);
}

.auth-modal-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: 3% auto;
    border-radius: 20px;
    width: 90%;
    max-width: 450px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-50px) scale(0.9); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

/* Compact Login/Signup Modal */

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

.google-btn {
    width: 100%;
    background: white;
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #333;
    margin-bottom: 15px;
}

.google-btn:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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

.form-group {
    margin-bottom: 15px;
}

.form-group input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s;
    outline: none;
    box-sizing: border-box;
}

.form-group input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

.form-group input::placeholder {
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

.forgot-link {
    text-align: center;
    margin-top: 10px;
}

.forgot-link a {
    color: #667eea;
    text-decoration: none;
    font-size: 13px;
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
a.btn.btn-outline {
    color: black;
}
a.btn.btn-primary {
    color: black;
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

/* Success/Error Messages */
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
/* Desktop Auth Buttons - Separate from nav menu */
.auth-buttons.desktop-only {
    display: flex;
    gap: 15px;
    align-items: center;
}

/* Plan Selection Styles */
.plan-option input[type="radio"]:checked + .plan-card {
    border-color: #667eea !important;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.plan-option:hover .plan-card {
    border-color: #667eea;
    transform: translateY(-2px);
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
@media (min-width: 769px) {
    .desktop-only {
        display: flex !important;
    }
    
    .mobile-only {
        display: none !important;
    }
}

/* Mobile */
@media (max-width: 480px) {
    .auth-modal-content {
        width: 95%;
        margin: 15% auto;
    }
    .auth-forms {
        padding: 20px;
    }
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

            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-stats {
                justify-content: center;
            }

            .hero-buttons {
                justify-content: center;
                flex-wrap: wrap;
            }

            .certificate-box {
                grid-template-columns: 1fr;
                padding: 40px;
                gap: 40px;
            }

            .editor-content {
                grid-template-columns: 1fr;
            }

            .features-header h2,
            .courses-header h2,
            .cta h2,
            .certificate-header h2,
            .code-editor-header h2 {
                font-size: 2rem;
            }

            .features-grid,
            .courses-grid {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .language-tabs {
                flex-wrap: wrap;
                gap: 8px;
            }

            .course-stats {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .course-info {
                gap: 15px;
            }
        }

        @media (max-width: 480px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .hero-stats {
                flex-direction: column;
                gap: 20px;
            }

            .modal-content {
                margin: 10% auto;
                padding: 20px;
                width: 95%;
            }

            .certificate-card {
                padding: 25px 20px;
            }

            .cert-title {
                font-size: 1.1rem;
            }

            .cert-course {
                font-size: 1.2rem;
            }

            .cert-recipient {
                font-size: 1.4rem;
            }

            .course-card {
                padding: 25px 20px 20px 20px;
            }

            .course-title {
                font-size: 1.7rem;
            }

            .course-info {
                flex-direction: column;
                gap: 8px;
                align-items: flex-start;
            }
        }

        /* Voice Recognition Animation */
        .mic-active {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: translateY(-50%) scale(1); }
            50% { transform: translateY(-50%) scale(1.1); }
            100% { transform: translateY(-50%) scale(1); }
        }

        /* Typing Animation */
        .typing::after {
            content: '|';
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
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
    </style>
</head>
<body data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">
    <!-- Header -->
    <header class="header">
        <nav class="nav-container">
        <a href="index.php" class="logo">
            <i class="fas fa-code"></i>
            CodeLearn
        </a>
        
        <!-- Search Box - Header me fixed -->
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
                        $userPlan = isset($_SESSION['plan']) ? $_SESSION['plan'] : 'free';
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
        <?php endif; ?>
    </nav>
</header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>CODE<br><span class="gradient-text">THE FUTURE</span></h1>
                <p class="hero-subtitle">Master AI-powered programming with interactive lessons & real-time guidance</p>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number">20+</div>
                        <div class="stat-label">Languages</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">98%</div>
                        <div class="stat-label">Success Rate</div>
                    </div>
                </div>
                
                <div class="hero-buttons">
                    <button class="btn btn-large btn-free" onclick="redirectToAuth()">
                        START CODING NOW - FREE
                    </button>                    
                    <button class="btn btn-large btn-demo" onclick="redirectToAuth()">
                        LIVE DEMO
                    </button>               
                </div>
            </div>
            
            <div class="code-editor">
                <div class="editor-header">
                    <div class="traffic-lights">
                        <div class="light red"></div>
                        <div class="light yellow"></div>
                        <div class="light green"></div>
                    </div>
                    <div class="editor-title">AI_Assistant.py</div>
                    <div class="live-indicator">LIVE</div>
                </div>
                
                <div class="code-content">
                    <div class="code-line">
                        <span class="line-number">1</span>
                        <span><span class="keyword">import</span> <span class="function">ai_brain</span> <span class="keyword">from</span> codelearn</span>
                    </div>
                    <div class="code-line">
                        <span class="line-number">2</span>
                        <span></span>
                    </div>
                    <div class="code-line">
                        <span class="line-number">3</span>
                        <span><span class="keyword">def</span> <span class="function">learn_with_ai</span>():</span>
                    </div>
                    <div class="code-line">
                        <span class="line-number">4</span>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp;student = <span class="string">"future_developer"</span></span>
                    </div>
                    <div class="code-line">
                        <span class="line-number">5</span>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp;ai_brain.<span class="function">teach</span>(student)</span>
                    </div>
                    <div class="code-line">
                        <span class="line-number">6</span>
                        <span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="keyword">return</span> <span class="string">"Success!"</span></span>
                    </div>
                </div>
                
                <div class="output-section">
                    <div class="output-header">AI OUTPUT</div>
                    <div class="output-content">
                        <div>✓ Student skills upgraded successfully!</div>
                        <div>✓ Career prospects: +250% boost</div>
                        <div>🔥 Next lesson ready...</div>
                    </div>
                </div>
                
                <button class="course-button" style="margin-top: 20px;" onclick="redirectToAuth()">
                    <i class="fas fa-play"></i> Experience Live Demo
                </button>
            </div>
        </div>
    </section>

    <!-- Certificate Section -->
    <section class="certificate-section">
        <div class="certificate-container">
            <div class="certificate-header">
                <h2>Provides Certificates</h2>
                <p>Earn valuable certificates that employers recognize and trust worldwide</p>
            </div>
            
            <div class="certificate-box">
                <div class="certificate-features">
                    <h3><i class="fas fa-trophy"></i>Professional Certification</h3>
                    <p style="color: #666; margin-bottom: 25px;">Verified by industry experts</p>
                    
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i>Blockchain-verified authenticity</li>
                        <li><i class="fas fa-check-circle"></i>Recognized by 500+ tech companies</li>
                        <li><i class="fas fa-check-circle"></i>Shareable on LinkedIn & social media</li>
                        <li><i class="fas fa-check-circle"></i>Permanent record in your portfolio</li>
                        <li><i class="fas fa-check-circle"></i>Skills assessment included</li>
                    </ul>
                    
                    <!-- <button class="view-sample-btn" onclick="redirectToAuth()">
                        View Sample Certificate
                    </button> -->
                </div>
                
                <div class="certificate-preview">
                    <div class="certificate-card">
                        <div class="cert-trophy">🏆</div>
                        <div class="cert-title">Certificate of Completion</div>
                        <div class="cert-course">Python Programming Mastery</div>
                        <div class="cert-recipient">Your Name Here</div>
                        <div class="cert-score">Score: 95%</div>
                        <div class="cert-verification">
                            <span>Verified</span>
                            <span>Blockchain Secured</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Code Editor Section -->
    <section class="code-editor-section">
        <div class="code-editor-container">
            <div class="code-editor-header">
                <h2>Powerful Live Code Editor</h2>
                <p>Practice coding with our advanced editor that supports 20+ programming languages</p>
            </div>
            
            <div class="editor-demo">
                <div class="demo-header">
                    <div class="demo-title">CodeLearn Editor</div>
                    <div class="status-indicators">
                        <span class="status-badge python-active">Python</span>
                        <span class="status-badge running">Running</span>
                    </div>
                </div>
                
                <div class="editor-content">
                    <div class="code-panel">
                        <div class="panel-header">Code</div>
                        <div class="demo-code">
                            <div><span style="color: #ff79c6;">def</span> <span style="color: #8be9fd;">fibonacci</span>(<span style="color: #f8f8f2;">n</span>):</div>
                            <div><span style="color: #ff79c6;">if</span> <span style="color: #f8f8f2;">n</span> <span style="color: #ff79c6;">&lt;=</span> <span style="color: #bd93f9;">1</span>:</div>
                            <div><span style="color: #ff79c6;">return</span> <span style="color: #f8f8f2;">n</span></div>
                            <div><span style="color: #ff79c6;">return</span> <span style="color: #8be9fd;">fibonacci</span>(<span style="color: #f8f8f2;">n-1</span>) <span style="color: #ff79c6;">+</span> <span style="color: #8be9fd;">fibonacci</span>(<span style="color: #f8f8f2;">n-2</span>)</div>
                            <div></div>
                            <div><span style="color: #6272a4;"># Calculate Fibonacci sequence</span></div>
                            <div><span style="color: #ff79c6;">for</span> <span style="color: #f8f8f2;">i</span> <span style="color: #ff79c6;">in</span> <span style="color: #8be9fd;">range</span>(<span style="color: #bd93f9;">10</span>):</div>
                            <div><span style="color: #8be9fd;">print</span>(<span style="color: #f1fa8c;">f"F({i}) = {fibonacci(i)}"</span>)</div>
                        </div>
                    </div>
                    
                    <div class="output-panel">
                        <div class="panel-header" style="color: #4ecdc4;">Output:</div>
                        <div class="demo-output">
                            <div class="output-line success">F(0) = 0</div>
                            <div class="output-line success">F(1) = 1</div>
                            <div class="output-line success">F(2) = 1</div>
                            <div class="output-line success">F(3) = 2</div>
                            <div class="output-line success">F(4) = 3</div>
                            <div class="output-line success">F(5) = 5</div>
                            <div class="output-line success">F(6) = 8</div>
                            <div class="output-line success">F(7) = 13</div>
                            <div class="output-line success">F(8) = 21</div>
                            <div class="output-line success">F(9) = 34</div>
                            
                            <div class="execution-info">✅ Execution completed in 0.004s</div>
                        </div>
                    </div>
                </div>
                
                <div class="language-tabs">
                    <button class="lang-tab active">Python</button>
                    <button class="lang-tab">JavaScript</button>
                    <button class="lang-tab">Java</button>
                    <button class="lang-tab">C++</button>
                    <button class="lang-tab">Go</button>
                    <button class="lang-tab">Rust</button>
                    <button class="lang-tab">TypeScript</button>
                    <button class="lang-tab">PHP</button>
                </div>
            </div>
            
            <div class="editor-features">
                <div class="editor-feature">
                    <div class="feature-icon-large">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Instant Execution</h3>
                    <p>Run code instantly with our cloud-based execution engine</p>
                </div>
                
                <div class="editor-feature">
                    <div class="feature-icon-large">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure Environment</h3>
                    <p>Safe sandboxed environment for all your coding experiments</p>
                </div>
                
                <div class="editor-feature">
                    <div class="feature-icon-large">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h3>AI Code Assistant</h3>
                    <p>Get intelligent suggestions and error explanations in real-time</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="features-container">
            <div class="features-header">
                <h2>Why Choose CodeLearn?</h2>
                <p>Experience the future of programming education with our cutting-edge features</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3>Interactive Code Editor</h3>
                    <p>Practice coding with our live editor that supports multiple programming languages with real-time execution</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3>AI-Powered Learning</h3>
                    <p>Get personalized help and explanations from our intelligent AI assistant that adapts to your learning style</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3>Certificates & Achievements</h3>
                    <p>Earn verified certificates and unlock achievements as you progress through your coding journey</p>
                </div>
            </div>
            
            <div class="features-grid" style="margin-top: 40px;">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Instant Execution</h3>
                    <p>Run code instantly with our cloud-based execution engine</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure Environment</h3>
                    <p>Safe sandboxed environment for all your coding experiments</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-microphone"></i>
                    </div>
                    <h3>Voice Command</h3>
                    <p>Hands-free coding experience with voice commands</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section class="courses" id="courses">
        <div class="courses-header">
            <h2>Popular Courses</h2>
            <p>Start your coding journey with our most loved programming courses</p>
        </div>
        
        <div class="courses-grid">
            <!-- Python Course -->
            <div class="course-card python">
                <div class="course-icon">
                    <i class="fab fa-python"></i>
                </div>
                <h3 class="course-title">Python</h3>
                <p class="course-description">Learn Python from basics to advanced concepts</p>
                <div class="course-stats">
                    <div class="rating"></div>
                    <div class="course-info">
                        <div class="info-item">
                            <i class="fas fa-book"></i>
                            <span>15 chapters</span>
                        </div>
                    </div>
                </div>
                <a href="./courses.php" class="course-button">
                    <i class="fas fa-play"></i> Start Learning
                </a>
            </div>

            <!-- Python AI Course -->
            
            <div class="course-card python-ai">
                <div class="pro-badge">
                    <i class="fas fa-crown"></i> PRO
                </div>
                <div class="course-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <h3 class="course-title">Python AI</h3>
                <p class="course-description">Advanced AI and Machine Learning with Python</p>
                <div class="course-stats">
                    <div class="rating"></div>
                    <div class="course-info">
                        <div class="info-item">
                            <i class="fas fa-book"></i>
                            <span>15 chapters</span>
                        </div>
                    </div>
                </div>
                <a href="./courses.php" class="course-button">
                    <i class="fas fa-play"></i> Start Learning
                </a>
            </div>

            <!-- JavaScript Course -->
            <div class="course-card javascript">
                <div class="course-icon">
                    <i class="fab fa-js-square"></i>
                </div>
                <h3 class="course-title">JavaScript</h3>
                <p class="course-description">Master JavaScript and modern web development</p>
                <div class="course-stats">
                    <div class="rating"></div>
                    <div class="course-info">
                        <div class="info-item">
                            <i class="fas fa-book"></i>
                            <span>20 chapters</span>
                        </div>
                    </div>
                </div>
                <a href="./courses.php" class="course-button">
                    <i class="fas fa-play"></i> Start Learning
                </a>
            </div>
            <!-- React Course -->
            <div class="course-card react">
                <div class="course-icon">
                    <i class="fab fa-react"></i>
                </div>
                <h3 class="course-title">React</h3>
                <p class="course-description">Build modern web applications with React</p>
                <div class="course-stats">
                    <div class="rating"></div>
                    <div class="course-info">
                        <div class="info-item">
                            <i class="fas fa-book"></i>
                            <span>12 chapters</span>
                        </div>
                    </div>
                </div>
                <a href="./courses.php" class="course-button">
                    <i class="fas fa-play"></i> Start Learning
                </a>
            </div>

            <!-- Node.js Course -->
            <div class="course-card nodejs">
                <div class="course-icon">
                    <i class="fab fa-node-js"></i>
                </div>
                <h3 class="course-title">Node.js</h3>
                <p class="course-description">Server-side JavaScript development</p>
                <div class="course-stats">
                    <div class="rating"></div>
                    <div class="course-info">
                        <div class="info-item">
                            <i class="fas fa-book"></i>
                            <span>18 chapters</span>
                        </div>
                    </div>
                </div>
                <a href="./courses.php" class="course-button">
                    <i class="fas fa-play"></i> Start Learning
                </a>
            </div>

            <!-- Java Course -->
            <div class="course-card java">
                <div class="course-icon">
                    <i class="fab fa-java"></i>
                </div>
                <h3 class="course-title">Java</h3>
                <p class="course-description">Enterprise-level Java programming</p>
                <div class="course-stats">
                    <div class="rating"></div>
                    <div class="course-info">
                        <div class="info-item">
                            <i class="fas fa-book"></i>
                            <span>17 chapters</span>
                        </div>
                    </div>
                </div>
                <a href="./courses.php" class="course-button">
                    <i class="fas fa-play"></i> Start Learning
                </a>
            </div>
        </div>
        
        <div style="text-align: center;">
            <a href="./courses.php" class="view-all-btn" onclick="viewAllCourses()">
                View All Courses <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="cta-container">
            <h2>Ready to Start Your Coding Journey?</h2>
            <p>Join millions of developers who have transformed their careers with CodeLearn</p>
            
            <div class="cta-buttons">
                <button class="btn btn-large btn-free" onclick="redirectToAuth()">Start Learning Now</button>
                <button class="btn btn-large btn-demo">Try Free Demo</button>
            </div>
        </div>
    </section>

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
                    <a href="./courses.php" onclick="redirectToAuth()">Python</a>
                    <a href="./courses.php" onclick="redirectToAuth()">JavaScript</a>
                    <a href="./courses.php" onclick="redirectToAuth()">React</a>
                    <a href="./courses.php" onclick="redirectToAuth()">Node.js</a>
                </div>
                
                <div class="footer-section">
                    <h4>Company</h4>
                    <a href="./about.php">About</a>
                    <a href="./contact.php">Contact</a>
                </div>
                
                <div class="footer-section">
                    <h4>Support</h4>
                    <a href="#" onclick="openModal('helpModal')">Help Center</a>
                    <!-- <a href="#privacy">Privacy Policy</a> -->
                    <a href="./trem-of-ser.php">Terms of Service</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 CodeLearn. All rights reserved. Made with for developers worldwide.</p>
            </div>
        </div>
    </footer>

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

<!-- Profile Edit Modal -->
<div id="profileModal" class="profile-modal">
    <div class="profile-modal-content">
        <div class="profile-modal-header">
            <span class="profile-modal-close" onclick="closeProfileModal()">&times;</span>
            <div class="profile-modal-title">Edit Profile</div>
            <div class="profile-modal-subtitle">Update your name</div>
        </div>
        
        <div class="profile-form">
            <div id="profileMessage" class="message">
                
            </div>
            
            <form id="profileForm" onsubmit="saveProfile(event)">
                <div class="profile-image-section">
                    <div class="current-profile-pic" id="profilePicDisplay">
                        <img id="profileImage" style="display: none;" class="profile-pic-preview">
                        <span id="profileInitials"><?php echo strtoupper(substr($userName, 0, 1)); ?></span>
                    </div>
                    <input type="file" id="profileImageInput" class="profile-image-input" accept="image/*" onchange="previewImage(event)">
                    <!-- <button type="button" class="change-image-btn" onclick="document.getElementById('profileImageInput').click()">
                        Change Photo
                    </button> -->
                </div>
                <!-- Name Input -->
                <div class="form-group">
                    <label class="form-label" for="profileName">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" id="profileName" name="name" class="form-input" value="<?php echo htmlspecialchars($userName); ?>" required minlength="2" maxlength="50">
                </div>

                <!-- Email Input (Read-only) -->
                <div class="form-group">
                    <label class="form-label" for="profileEmail">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="profileEmail" class="form-input" value="<?php echo htmlspecialchars(isset($_SESSION['email']) ? $_SESSION['email'] : 'user@codelearn.com'); ?>" readonly style="background: #f5f5f5; cursor: not-allowed;">
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
<script>

// ==================== MOBILE MENU ====================
function toggleMenu() {
    const navMenu = document.getElementById('navMenu');
    const hamburger = document.querySelector('.hamburger');
    navMenu.classList.toggle('active');
    hamburger.classList.toggle('active');
}

// ==================== USER DROPDOWN ====================
document.addEventListener('DOMContentLoaded', function() {
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

function toggleDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('userDropdown');
    const userProfile = document.querySelector('.user-profile');
    
    dropdown.classList.toggle('active');
    userProfile.classList.toggle('active');
}

// ==================== MODAL FUNCTIONS ====================
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}

// ==================== AUTH MODAL ====================
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
                window.location.href = result.redirect || 'index.php';
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

function googleAuth() {
    showMessage('Google authentication coming soon!', 'error');
}

// ==================== REDIRECT FUNCTION (FIXED) ====================
function redirectToAuth() {
    window.location.href = './live-code.php';
}

function startLearning(course) {
    <?php if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])): ?>
    openAuthModal();
    <?php else: ?>
    window.location.href = 'course.php?id=' + course;
    <?php endif; ?>
}

function viewAllCourses() {
    <?php if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])): ?>
    openAuthModal();
    <?php else: ?>
    window.location.href = 'courses.php';
    <?php endif; ?>
}

// ==================== PROFILE MODAL ====================
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
    messageDiv.style.display = 'none';
    messageDiv.textContent = '';
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
            showProfileMessage('Profile updated successfully!', 'success');
            
            document.querySelectorAll('.user-name, .dropdown-user-name').forEach(el => {
                el.textContent = name;
            });
            
            const initial = name.charAt(0).toUpperCase();
            document.querySelectorAll('.profile-pic, .dropdown-profile-pic').forEach(el => {
                el.textContent = initial;
            });
            
            setTimeout(() => {
                closeProfileModal();
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

// ==================== LOGOUT FUNCTION ====================
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
        closeModalLogout();
    };

    document.getElementById('confirmBtn').onclick = function() {
        this.innerHTML = '<div style="width: 16px; height: 16px; border: 2px solid white; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>';
        this.disabled = true;
        
        fetch('logout.php', { method: 'POST' })
        .then(() => {
            closeModalLogout();
            showLogoutToast();
            setTimeout(() => window.location.href = 'index.php', 2000);
        })
        .catch(() => {
            window.location.href = 'logout.php';
        });
    };

    function closeModalLogout() {
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
        if (e.target === modal) closeModalLogout();
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

// ==================== CODE TYPING ANIMATION (MAIN FIX) ====================
function typeCode() {
    const outputContent = document.querySelector('.output-content');
    if (!outputContent) {
        console.error('Output content not found');
        return;
    }
    
    const messages = [
        '✓ Student skills upgraded successfully!',
        '✓ Career prospects: +250% boost',
        '🔥 Next lesson ready...'
    ];
    
    let messageIndex = 0;
    let charIndex = 0;
    
    function typeMessage() {
        if (messageIndex < messages.length) {
            const currentMessage = messages[messageIndex];
            const messageElement = outputContent.children[messageIndex];
            
            if (!messageElement) {
                console.error('Message element not found at index:', messageIndex);
                return;
            }
            
            if (charIndex < currentMessage.length) {
                messageElement.textContent = currentMessage.substring(0, charIndex + 1) + '|';
                charIndex++;
                setTimeout(typeMessage, 50);
            } else {
                messageElement.textContent = currentMessage;
                messageIndex++;
                charIndex = 0;
                setTimeout(typeMessage, 1000);
            }
        } else {
            setTimeout(() => {
                messageIndex = 0;
                charIndex = 0;
                Array.from(outputContent.children).forEach(el => el.textContent = '');
                typeMessage();
            }, 3000);
        }
    }
    
    Array.from(outputContent.children).forEach(el => el.textContent = '');
    typeMessage();
}

// ==================== DOM READY EVENTS ====================
document.addEventListener('DOMContentLoaded', function() {
    // Courses smooth scroll
    const coursesLinks = document.querySelectorAll('a[href="#courses"]');
    coursesLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const navMenu = document.getElementById('navMenu');
            const hamburger = document.querySelector('.hamburger');
            if (navMenu && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            }
            
            const coursesSection = document.getElementById('courses');
            if (coursesSection) {
                coursesSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Logout link setup
    const logoutLink = document.querySelector('a[href="logout.php"]');
    if (logoutLink) {
        logoutLink.href = '#';
        logoutLink.onclick = function(e) {
            e.preventDefault();
            logout();
            return false;
        };
    }
    
    // Language tabs
    const langTabs = document.querySelectorAll('.lang-tab');
    langTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            langTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Course card hover
    const courseCards = document.querySelectorAll('.course-card');
    courseCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// ==================== WINDOW LOAD EVENTS ====================
window.addEventListener('load', function() {
    // Start typing animation
    setTimeout(typeCode, 2000);
    
    // Animate feature cards
    const cards = document.querySelectorAll('.feature-card, .course-card, .editor-feature');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
});

// ==================== OTHER EVENT LISTENERS ====================
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
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    if (header) {
        if (window.scrollY > 100) {
            header.style.background = 'rgba(255, 255, 255, 0.95)';
            header.style.backdropFilter = 'blur(10px)';
        } else {
            header.style.background = 'white';
            header.style.backdropFilter = 'none';
        }
    }
});

const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// ==================== PASSWORD TOGGLE ====================
function togglePasswordVisibility(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = document.getElementById(iconId);
    
    if (passwordInput && toggleIcon) {
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
}
</script>
</body>
</html>