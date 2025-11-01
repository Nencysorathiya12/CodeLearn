<?php
session_start();

// Database configuration
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

// Get all published courses with lesson count (limit to 15)
$stmt = $pdo->query("
    SELECT c.*, 
    (SELECT COUNT(*) FROM lessons WHERE course_id = c.course_id) as lesson_count 
    FROM courses c 
    WHERE c.status='published' 
    ORDER BY c.created_at DESC 
    LIMIT 15
");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Course icons mapping with specific colors
$course_icons = [
    'python' => ['icon' => '🐍', 'color' => '#043258ff'],
    'java' => ['icon' => '☕', 'color' => '#007396'],
    'javascript' => ['icon' => '⚡', 'color' => '#F7DF1E'],
    'node' => ['icon' => '💚', 'color' => '#339933'],
    'data science' => ['icon' => '📊', 'color' => '#FF6F00'],
    'datascience' => ['icon' => '📊', 'color' => '#FF6F00'],
    'react' => ['icon' => '⚛️', 'color' => '#61DAFB'],
    'html' => ['icon' => '🌐', 'color' => '#E34F26'],
    'css' => ['icon' => '🎨', 'color' => '#1572B6'],
    'express' => ['icon' => '🚀', 'color' => '#000000'],
    'sql' => ['icon' => '🗄️', 'color' => '#4479A1'],
    'database' => ['icon' => '💾', 'color' => '#4479A1'],
    'php' => ['icon' => '🐘', 'color' => '#777BB4'],
    'dsa' => ['icon' => '🔢', 'color' => '#FF6B6B'],
    'c++' => ['icon' => '⚙️', 'color' => '#00599C'],
    'cpp' => ['icon' => '⚙️', 'color' => '#00599C'],
    'c programming' => ['icon' => '©️', 'color' => '#A8B9CC'],
    'angular' => ['icon' => '🅰️', 'color' => '#DD0031'],
    'mongo' => ['icon' => '🍃', 'color' => '#47A248'],
    'ai' => ['icon' => '🤖', 'color' => '#FF6F00'],
    'artificial intelligence' => ['icon' => '🤖', 'color' => '#FF6F00'],
    'vue' => ['icon' => '💚', 'color' => '#4FC08D'],
    'cyber' => ['icon' => '🔒', 'color' => '#000000'],
    'security' => ['icon' => '🛡️', 'color' => '#000000'],
    'default' => ['icon' => '📚', 'color' => '#6B7280']
];

function getCourseIcon($title) {
    global $course_icons;
    $title_lower = strtolower($title);
    foreach ($course_icons as $key => $data) {
        if (strpos($title_lower, $key) !== false) {
            return $data;
        }
    }
    return $course_icons['default'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Popular Courses - CodeLearn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

       body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #98a3f8ff 0%, #8941e0ff 50%, #120441ff 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            padding-top: 90px;
            padding-left: 0;   /* Change from 20px to 0 */
            padding-right: 0;  /* Change from 20px to 0 */
            padding-bottom: 40px;
            position: relative;
            overflow-x: hidden;
            margin: 0;  /* Add this */
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.2), transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(96, 165, 250, 0.2), transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(37, 99, 235, 0.15), transparent 50%);
            z-index: -1;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        /* Header Styles */
/* Header Styles - FULL WIDTH FIX */
/* ========== HEADER STYLES ========== */
.header {
    background: white;
    padding: 15px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    position: fixed;
    width: 100%;
    top: 0;
    left: 0;      /* ADD THIS */
    right: 0;     /* ADD THIS */
    z-index: 1000;
    height: 90px; /* ADD THIS - Fixed height */
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    width: 100%;  /* ADD THIS */
    height: 100%; /* ADD THIS */
}
/* ========== MAIN CONTENT STYLES ========== */
.container {
    max-width: 1200px;
    margin: 90px auto 40px;  /* Change from 100px to 90px */
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-outline {
    border: 2px solid #6c63ff;
    color: #6c63ff;
    background: transparent;
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

/* Responsive */
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
        padding-top: 0;
        transition: left 0.3s;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        overflow-y: auto;
        z-index: 999;
    }

    .nav-menu.active {
        left: 0;
    }

    .nav-menu li {
        width: 100%;
        text-align: center;
        margin: 0;
        padding: 0;
    }

    .nav-menu li a {
        display: block;
        padding: 10px 20px;
        width: 100%;
    }

    .hamburger {
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

    .search-container {
        max-width: 200px;
    }

    .search-box {
        width: 100%;
    }

    .desktop-only {
        display: none !important;
    }

    .mobile-only {
        display: block !important;
    }

    .auth-buttons-mobile {
        display: flex !important;
        flex-direction: column;
        gap: 10px;
        padding: 15px 20px;
        width: 90%;
    }

    .auth-buttons-mobile .btn {
        width: 100%;
        text-align: center;
    }

    .logout-link {
        color: #e74c3c !important;
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

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header-section {
            text-align: center;
            margin-bottom: 60px;
            color: white;
        }

        .header-section h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 16px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .header-section p {
            font-size: 20px;
            opacity: 0.95;
            font-weight: 400;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 40px;
        }

        @media (max-width: 1400px) {
            .courses-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 1024px) {
            .courses-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .floating-icons {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .tech-icon {
            position: absolute;
            font-size: 50px;
            opacity: 0.08;
            animation: blink-slow 2s ease-in-out infinite;
            font-weight: bold;
            color: rgba(255, 255, 255, 0.85);
            font-family: 'Courier New', monospace;
        }

        .tech-icon:nth-child(1) { top: 8%; left: 5%; animation-delay: 0s; font-size: 45px; }
        .tech-icon:nth-child(2) { top: 22%; right: 10%; animation-delay: 1s; font-size: 55px; }
        .tech-icon:nth-child(3) { top: 52%; left: 8%; animation-delay: 2s; font-size: 48px; }
        .tech-icon:nth-child(4) { bottom: 15%; right: 7%; animation-delay: 3s; font-size: 52px; }
        .tech-icon:nth-child(5) { top: 35%; left: 15%; animation-delay: 1.5s; font-size: 46px; }
        .tech-icon:nth-child(6) { bottom: 32%; left: 10%; animation-delay: 2.5s; font-size: 50px; }
        .tech-icon:nth-child(7) { top: 62%; right: 15%; animation-delay: 3.5s; font-size: 44px; }
        .tech-icon:nth-child(8) { top: 28%; right: 20%; animation-delay: 0.5s; font-size: 58px; }
        .tech-icon:nth-child(9) { bottom: 38%; right: 25%; animation-delay: 4s; font-size: 47px; }
        .tech-icon:nth-child(10) { top: 15%; left: 25%; animation-delay: 2s; font-size: 53px; }
        .tech-icon:nth-child(11) { top: 45%; right: 6%; animation-delay: 1.2s; font-size: 49px; }
        .tech-icon:nth-child(12) { bottom: 22%; left: 22%; animation-delay: 3.2s; font-size: 51px; }
        .tech-icon:nth-child(13) { top: 70%; left: 18%; animation-delay: 0.8s; font-size: 46px; }
        .tech-icon:nth-child(14) { top: 12%; right: 30%; animation-delay: 2.8s; font-size: 54px; }
        .tech-icon:nth-child(15) { bottom: 45%; left: 30%; animation-delay: 1.8s; font-size: 48px; }
        .tech-icon:nth-child(16) { top: 58%; right: 32%; animation-delay: 3.8s; font-size: 50px; }
        .tech-icon:nth-child(17) { top: 25%; left: 35%; animation-delay: 0.3s; font-size: 52px; }
        .tech-icon:nth-child(18) { bottom: 55%; right: 12%; animation-delay: 2.3s; font-size: 47px; }
        .tech-icon:nth-child(19) { top: 75%; right: 8%; animation-delay: 1.3s; font-size: 49px; }
        .tech-icon:nth-child(20) { top: 42%; left: 12%; animation-delay: 3.3s; font-size: 45px; }
        .tech-icon:nth-child(21) { bottom: 28%; right: 18%; animation-delay: 0.7s; font-size: 53px; }
        .tech-icon:nth-child(22) { top: 18%; left: 40%; animation-delay: 2.7s; font-size: 46px; }
        .tech-icon:nth-child(23) { bottom: 12%; left: 35%; animation-delay: 1.7s; font-size: 51px; }
        .tech-icon:nth-child(24) { top: 65%; left: 5%; animation-delay: 3.7s; font-size: 48px; }
        .tech-icon:nth-child(25) { top: 32%; right: 35%; animation-delay: 0.9s; font-size: 50px; }
        .tech-icon:nth-child(26) { bottom: 48%; left: 6%; animation-delay: 2.9s; font-size: 47px; }
        .tech-icon:nth-child(27) { top: 50%; right: 28%; animation-delay: 1.9s; font-size: 52px; }
        .tech-icon:nth-child(28) { bottom: 60%; right: 22%; animation-delay: 3.9s; font-size: 49px; }
        .tech-icon:nth-child(29) { top: 38%; left: 42%; animation-delay: 0.6s; font-size: 54px; }
        .tech-icon:nth-child(30) { bottom: 35%; right: 38%; animation-delay: 2.6s; font-size: 46px; }
        .tech-icon:nth-child(31) { top: 55%; left: 28%; animation-delay: 1.6s; font-size: 48px; }
        .tech-icon:nth-child(32) { top: 48%; right: 42%; animation-delay: 3.6s; font-size: 50px; }

        @keyframes blink-slow {
            0%, 100% {
                opacity: 0.05;
            }
            50% {
                opacity: 0.12;
            }
        }

        .container {
            position: relative;
            z-index: 1;
        }

        .course-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            border: 1px solid #F3F4F6;
        }

        .course-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .course-header {
            padding: 24px 24px 16px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background: white;
            flex: 1;
        }

        .course-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: iconPulse 2s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2), 0 0 20px rgba(255, 255, 255, 0.3);
            }
        }

        .course-info {
            width: 100%;
        }

        .course-title {
            font-size: 20px;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .course-subtitle {
            font-size: 13px;
            color: #6B7280;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .course-description {
            color: #6B7280;
            font-size: 13px;
            line-height: 1.5;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .course-body {
            padding: 0 24px 24px 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .course-lessons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #6B7280;
            font-size: 14px;
            padding: 10px;
            background: #F9FAFB;
            border-radius: 10px;
        }

        .course-lessons i {
            color: #3B82F6;
        }

        .course-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .course-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        .course-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(255, 165, 0, 0.3);
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .course-badge i {
            font-size: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 80px 40px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .empty-state-icon {
            font-size: 72px;
            margin-bottom: 20px;
        }

        .empty-state h2 {
            font-size: 28px;
            margin-bottom: 12px;
            color: #1F2937;
            font-weight: 700;
        }

        .empty-state p {
            font-size: 16px;
            color: #6B7280;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(-4px);
        }

        /* Auth Modal Styles */
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

        @media (max-width: 768px) {
            .courses-grid {
                grid-template-columns: 1fr;
            }

            .header-section h1 {
                font-size: 32px;
            }

            .header-section p {
                font-size: 15px;
            }

            .course-card {
                aspect-ratio: auto;
            }

            .course-title {
                font-size: 18px;
            }

            .course-header {
                padding: 20px;
            }

            .course-body {
                padding: 0 20px 20px 20px;
            }

            .course-icon {
                width: 60px;
                height: 60px;
                font-size: 32px;
            }

            .course-description {
                font-size: 13px;
            }

            .course-badge {
                top: 12px;
                right: 12px;
                padding: 5px 10px;
                font-size: 10px;
            }

            .auth-modal-content {
                width: 95%;
                margin: 15% auto;
            }
            .auth-forms {
                padding: 20px;
            }
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
    </style>
</head>
<body data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">
    <!-- Floating Tech Icons -->
    <div class="floating-icons">
        <div class="tech-icon">&lt;/&gt;</div>
        <div class="tech-icon">{ }</div>
        <div class="tech-icon">[ ]</div>
        <div class="tech-icon">&lt;div&gt;</div>
        <div class="tech-icon">fn( )</div>
        <div class="tech-icon">=&gt;</div>
        <div class="tech-icon">AI</div>
        <div class="tech-icon">DB</div>
        <div class="tech-icon">API</div>
        <div class="tech-icon">JS</div>
        <div class="tech-icon">CSS</div>
        <div class="tech-icon">@</div>
        <div class="tech-icon">&lt;?&gt;</div>
        <div class="tech-icon">$</div>
        <div class="tech-icon">#</div>
        <div class="tech-icon">*</div>
        <div class="tech-icon">( )</div>
        <div class="tech-icon">++</div>
        <div class="tech-icon">!=</div>
        <div class="tech-icon">===</div>
        <div class="tech-icon">if</div>
        <div class="tech-icon">for</div>
        <div class="tech-icon">var</div>
        <div class="tech-icon">def</div>
        <div class="tech-icon">try</div>
        <div class="tech-icon">new</div>
        <div class="tech-icon">class</div>
        <div class="tech-icon">import</div>
        <div class="tech-icon">async</div>
        <div class="tech-icon">return</div>
        <div class="tech-icon">null</div>
        <div class="tech-icon">true</div>
    </div>
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
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Mobile User Menu Items -->
                    <li class="mobile-only"><a href="#" onclick="openProfileModal(); event.preventDefault(); return false;"><i class="fas fa-user"></i> Profile</a></li>
                    <li class="mobile-only"><a href="pricing.php"><i class="fas fa-crown"></i> Plans & Pricing</a></li>
                    <li class="mobile-only"><a href="#" onclick="logout(); event.preventDefault(); return false;" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <!-- Mobile Auth Buttons -->
                    <li class="auth-buttons-mobile mobile-only">
                        <a href="#" onclick="openAuthModal(); return false;" class="btn btn-outline">Sign In</a>
                        <!-- <a href="#" onclick="openAuthModal(); return false;" class="btn btn-primary">Get Started</a> -->
                    </li>
                <?php endif; ?>
            </ul>
            
            <!-- Desktop Auth Buttons -->
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="auth-buttons desktop-only">
                    <a href="#" onclick="openAuthModal(); return false;" class="btn btn-outline">Sign In</a>
                    <!-- <a href="#" onclick="openAuthModal(); return false;" class="btn btn-primary">Get Started</a> -->
                </div>
            <?php endif; ?>
            
            <!-- Desktop User Profile -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-profile desktop-only" id="userProfileBtn">
                    <div class="profile-pic"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></div>
                    <div class="user-info">
                        <div class="user-name">
                            <?php echo $_SESSION['name']; ?>
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
                            <div class="dropdown-profile-pic"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></div>
                            <div class="dropdown-user-name"><?php echo $_SESSION['name']; ?></div>
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

    <div class="container">
        <!-- <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a> -->

        <div class="header-section">
            <h1>Popular Courses</h1>
            <p>Start your coding journey with our most loved programming courses</p>
        </div>

        <?php if (count($courses) > 0): ?>
        <div class="courses-grid">
            <?php 
            foreach ($courses as $course): 
                $iconData = getCourseIcon($course['title']);
            ?>
            <div class="course-card">
                <?php if ($course['type'] === 'pro'): ?>
                <div class="course-badge">
                    <i class="fas fa-crown"></i> PRO
                </div>
                <?php endif; ?>

                <div class="course-header">
                    <div class="course-icon" style="background: <?php echo $iconData['color']; ?>20; color: <?php echo $iconData['color']; ?>;">
                        <?php echo $iconData['icon']; ?>
                    </div>
                    <div class="course-info">
                        <h2 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h2>
                        <p class="course-subtitle">Beginner to Advanced</p>
                        <p class="course-description">
                            <?php echo htmlspecialchars($course['description']); ?>
                        </p>
                    </div>
                </div>

                <div class="course-body">
                    <div class="course-lessons">
                        <i class="fas fa-book-open"></i>
                        <span><strong><?php echo $course['lesson_count']; ?></strong> chapters</span>
                    </div>

                    <button class="course-btn" style="background: <?php echo $iconData['color']; ?>;" onclick="startLearning(<?php echo $course['course_id']; ?>, '<?php echo htmlspecialchars($course['title']); ?>', '<?php echo $course['type']; ?>')">
                        <i class="fas fa-play"></i>
                        Continue Learning
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">📚</div>
            <h2>No Courses Available</h2>
            <p>Check back soon for new courses!</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Login/Signup Modal -->
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

    <!-- Help Modal -->
    <div id="helpModal" class="modal">
        <div class="modal-content">
            <a href="#" onclick="openModal('helpModal'); event.stopPropagation(); return false;" class="dropdown-item">
            <i class="fas fa-question-circle"></i>
            <span>Help & Support</span>
        </a>
            <div class="support-options">
                <div class="support-option" onclick="window.open('mailto:support@codelearn.com')">
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
            </div>
        </div>
    </div>

    <!-- Profile Edit Modal -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div id="profileModal" class="profile-modal">
        <div class="profile-modal-content">
            <div class="profile-modal-header">
                <span class="profile-modal-close" onclick="closeProfileModal()">&times;</span>
                <div class="profile-modal-title">Edit Profile</div>
                <div class="profile-modal-subtitle">Update your name</div>
            </div>
            
            <div class="profile-form">
                <div id="profileMessage" class="message"></div>
                
                <form id="profileForm" onsubmit="saveProfile(event)">
                    <div class="profile-image-section">
                        <div class="current-profile-pic" id="profilePicDisplay">
                            <span id="profileInitials"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="profileName">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <input type="text" id="profileName" name="name" class="form-input" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" required minlength="2" maxlength="50">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="profileEmail">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" id="profileEmail" class="form-input" value="<?php echo htmlspecialchars(isset($_SESSION['email']) ? $_SESSION['email'] : 'user@codelearn.com'); ?>" readonly style="background: #f5f5f5; cursor: not-allowed;">
                        <small style="color: #666; font-size: 12px; margin-top: 4px; display: block;">Email cannot be changed</small>
                    </div>

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
    <?php endif; ?>


    <script>
// Mobile menu toggle
function toggleMenu() {
    const navMenu = document.getElementById('navMenu');
    const hamburger = document.querySelector('.hamburger');
    navMenu.classList.toggle('active');
    hamburger.classList.toggle('active');
}

// User dropdown toggle
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

// Profile Modal (add if you want profile editing)
function openProfileModal() {
    alert('Profile modal functionality - coming soon!');
}

// Logout function
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
        closeLogoutModal();
    };

    document.getElementById('confirmBtn').onclick = function() {
        this.innerHTML = '<div style="width: 16px; height: 16px; border: 2px solid white; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>';
        this.disabled = true;
        
        fetch('logout.php', { method: 'POST' })
        .then(() => {
            closeLogoutModal();
            showLogoutToast();
            setTimeout(() => window.location.href = 'index.php', 2000);
        })
        .catch(() => {
            window.location.href = 'logout.php';
        });
    };

    function closeLogoutModal() {
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
        if (e.target === modal) closeLogoutModal();
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
                        window.location.href = result.redirect || 'courses.php';
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

        // Course Learning Function
        function startLearning(courseId, courseTitle, courseType) {
            <?php if (isset($_SESSION['user_id'])): ?>
                const userPlan = '<?php echo isset($_SESSION['plan']) ? $_SESSION['plan'] : 'free'; ?>';
                
                if (courseType === 'pro' && (userPlan === 'free' || !userPlan)) {
                    showUpgradeModal();
                } else {
                    window.location.href = 'lessons.php?course_id=' + courseId;
                }
            <?php else: ?>
                // Show auth modal instead of alert
                openAuthModal();
            <?php endif; ?>
        }

        function showUpgradeModal() {
            window.location.href = 'pricing.php';
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const authModal = document.getElementById('authModal');
            if (event.target === authModal) {
                closeAuthModal();
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
            showProfileMessage('Profile updated successfully!', 'success');
            
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
                location.reload(); // Refresh to update session
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
    </script>
</body>
</html>