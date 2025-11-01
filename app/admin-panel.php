<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$admin_name = $_SESSION['admin_name'];
$admin_email = $_SESSION['admin_email'];
$admin_initial = strtoupper(substr($admin_name, 0, 1));

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

// Get dashboard statistics
$stats = [];

// Total users count
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$stats['total_users'] = $stmt->fetch()['total_users'];

// Users registered today
$stmt = $pdo->query("SELECT COUNT(*) as today_users FROM users WHERE DATE(created_at) = CURDATE()");
$stats['today_users'] = $stmt->fetch()['today_users'];

// Users registered this month
$stmt = $pdo->query("SELECT COUNT(*) as month_users FROM users WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$stats['month_users'] = $stmt->fetch()['month_users'];

// Users registered last month
$stmt = $pdo->query("SELECT COUNT(*) as last_month_users FROM users WHERE MONTH(created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
$stats['last_month_users'] = $stmt->fetch()['last_month_users'];

// Calculate growth percentage
$current_month = $stats['month_users'];
$last_month = $stats['last_month_users'];
$growth_percentage = $last_month > 0 ? round((($current_month - $last_month) / $last_month) * 100) : ($current_month > 0 ? 100 : 0);

// Get recent users (with plan)
$stmt = $pdo->query("SELECT name, email, plan, created_at FROM users ORDER BY created_at DESC LIMIT 10");
$recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add feedback statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_feedback FROM feedback");
$feedback_count_result = $stmt->fetch();
$stats['total_feedback'] = $feedback_count_result ? $feedback_count_result['total_feedback'] : 0;

$stmt = $pdo->query("SELECT AVG(rating) as avg_rating FROM feedback");
$avg_rating_result = $stmt->fetch();
$stats['avg_rating'] = $avg_rating_result['avg_rating'] ? round($avg_rating_result['avg_rating'], 1) : 0;

$stmt = $pdo->query("SELECT COUNT(*) as today_feedback FROM feedback WHERE DATE(created_at) = CURDATE()");
$today_feedback_result = $stmt->fetch();
$stats['today_feedback'] = $today_feedback_result ? $today_feedback_result['today_feedback'] : 0;

// Get user registration data for last 7 days for chart
$stmt = $pdo->query("
    SELECT DATE(created_at) as date, COUNT(*) as count 
    FROM users 
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date ASC
");
$chart_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fill missing dates with zero
$last_7_days = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $found = false;
    foreach ($chart_data as $data) {
        if ($data['date'] === $date) {
            $last_7_days[] = ['date' => $date, 'count' => $data['count']];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $last_7_days[] = ['date' => $date, 'count' => 0];
    }
}

// Get online/active users (users who logged in within last 5 minutes)
$stmt = $pdo->query("SELECT COUNT(*) as online_users FROM users WHERE last_activity >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
$online_result = $stmt->fetch();
$stats['online_users'] = $online_result ? $online_result['online_users'] : 0;

// ADD THIS - Payment Statistics
try {
    // Total revenue
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total_revenue FROM payment WHERE status = 'completed'");
    $revenue_result = $stmt->fetch();
    $stats['total_revenue'] = $revenue_result ? $revenue_result['total_revenue'] : 0;
    
    // This month revenue
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as month_revenue FROM payment WHERE status = 'completed' AND MONTH(payment_date) = MONTH(CURRENT_DATE()) AND YEAR(payment_date) = YEAR(CURRENT_DATE())");
    $month_revenue_result = $stmt->fetch();
    $stats['month_revenue'] = $month_revenue_result ? $month_revenue_result['month_revenue'] : 0;
    
    // Last month revenue
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as last_month_revenue FROM payment WHERE status = 'completed' AND MONTH(payment_date) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(payment_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
    $last_month_revenue_result = $stmt->fetch();
    $stats['last_month_revenue'] = $last_month_revenue_result ? $last_month_revenue_result['last_month_revenue'] : 0;
    
    // Total payments count
    $stmt = $pdo->query("SELECT COUNT(*) as total_payments FROM payment WHERE status = 'completed'");
    $payment_count_result = $stmt->fetch();
    $stats['total_payments'] = $payment_count_result ? $payment_count_result['total_payments'] : 0;
    
    // Plan distribution
    $stmt = $pdo->query("SELECT plan, COUNT(*) as count FROM users WHERE plan IN ('pro', 'team') GROUP BY plan");
    $plan_distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stats['pro_users'] = 0;
    $stats['team_users'] = 0;
    foreach ($plan_distribution as $dist) {
        if ($dist['plan'] === 'pro') $stats['pro_users'] = $dist['count'];
        if ($dist['plan'] === 'team') $stats['team_users'] = $dist['count'];
    }
    
    // Revenue growth calculation
    $revenue_growth = $stats['last_month_revenue'] > 0 
        ? round((($stats['month_revenue'] - $stats['last_month_revenue']) / $stats['last_month_revenue']) * 100) 
        : ($stats['month_revenue'] > 0 ? 100 : 0);
    $stats['revenue_growth'] = $revenue_growth;
    
} catch (Exception $e) {
    $stats['total_revenue'] = 0;
    $stats['month_revenue'] = 0;
    $stats['total_payments'] = 0;
    $stats['pro_users'] = 0;
    $stats['team_users'] = 0;
    $stats['revenue_growth'] = 0;
}

// Revenue calculation - UPDATE THIS LINE (around line 77)
$revenue = $stats['month_revenue']; // REPLACE: $revenue = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeLearn Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #F8F9FA;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #1E293B 0%, #0F172A 100%);
            transition: all 0.3s ease;
            z-index: 1000;
            padding: 30px 0;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 0 30px 40px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-text {
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 24px;
            font-weight: 800;
            transition: all 0.3s ease;
            letter-spacing: -0.5px;
        }

        .sidebar.collapsed .logo-text {
            opacity: 0;
            width: 0;
        }

        .toggle-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: #E2E8F0;
            font-size: 18px;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
        }

        .toggle-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        .sidebar-menu {
            padding: 0 20px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: #94A3B8;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            font-weight: 500;
            font-size: 15px;
            margin-bottom: 6px;
            border-radius: 12px;
            position: relative;
        }

        .menu-item:hover {
            color: #F1F5F9;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }

        .menu-item.active {
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            color: white;
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
        }

        .menu-text {
            transition: all 0.3s ease;
            margin-left: 12px;
        }

        .sidebar.collapsed .menu-text {
            opacity: 0;
            width: 0;
            margin-left: 0;
        }

        .menu-icon {
            font-size: 18px;
            min-width: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-icon i {
            transition: all 0.3s ease;
        }

        .menu-item:hover .menu-icon i {
            transform: scale(1.1);
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: all 0.3s ease;
            padding: 30px;
            background: #F8F9FA;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        /* Header */
        .header-card {
            background: white;
            padding: 28px 36px;
            border-radius: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
        }

        .header-title {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #1E293B 0%, #3B82F6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
        }

        .user-details h3 {
            color: #1F2937;
            font-weight: 600;
            margin-bottom: 2px;
            font-size: 15px;
        }

        .user-details p {
            color: #9CA3AF;
            font-size: 13px;
        }

        /* Content Area */
        .content {
            display: none;
        }

        .content.active {
            display: block;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            position: relative;
            transition: all 0.3s ease;
            border: 1px solid #E5E7EB;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--card-color) 0%, var(--card-color-light) 100%);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
            border-color: var(--card-color);
        }

        .stat-card.blue { --card-color: #3B82F6; --card-color-light: #60A5FA; }
        .stat-card.purple { --card-color: #8B5CF6; --card-color-light: #A78BFA; }
        .stat-card.green { --card-color: #10B981; --card-color-light: #34D399; }
        .stat-card.orange { --card-color: #F59E0B; --card-color-light: #FBBF24; }

        .stat-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .stat-info h2 {
            font-size: 36px;
            font-weight: 800;
            color: #1F2937;
            margin-bottom: 6px;
            line-height: 1;
        }

        .stat-info p {
            color: #6B7280;
            font-weight: 500;
            font-size: 14px;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            background: var(--card-color);
            color: white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        }

        .stat-growth {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
        }

        .stat-growth.negative {
            background: rgba(239, 68, 68, 0.12);
            color: #DC2626;
        }

        /* Live Users Badge */
        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background: #10B981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Chart Container */
        .chart-container {
            background: white;
            padding: 32px;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            border: 1px solid #E5E7EB;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .chart-title {
            font-size: 20px;
            font-weight: 700;
            color: #1F2937;
        }

        /* Data Tables */
        .data-table {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            border: 1px solid #E5E7EB;
        }

        .table-header {
            padding: 28px 32px;
            border-bottom: 1px solid #F3F4F6;
            background: #FAFBFC;
        }

        .table-title {
            font-size: 20px;
            font-weight: 700;
            color: #1F2937;
        }

        /* Recent Users Table */
        .recent-users-table {
            width: 100%;
        }

        .recent-users-table th,
        .recent-users-table td {
            padding: 18px 20px;
            text-align: left;
            border-bottom: 1px solid #F3F4F6;
        }

        .recent-users-table th {
            background: #FAFBFC;
            color: #6B7280;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .recent-users-table td {
            color: #4B5563;
            font-size: 14px;
        }

        .recent-users-table tr:hover {
            background: #FAFBFC;
        }

        .recent-users-table tr:last-child td {
            border-bottom: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 40px;
            color: #9CA3AF;
        }

        .empty-state-icon {
            font-size: 72px;
            margin-bottom: 20px;
            opacity: 0.6;
        }

        .empty-state h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #6B7280;
            font-weight: 700;
        }

        .empty-state p {
            font-size: 15px;
            opacity: 0.8;
        }

        .logout-btn {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            margin-top: 8px;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }

        .logout-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        .user-avatar-small {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.2);
        }

        /* Mobile Responsive */
        .mobile-menu-btn {
            display: none;
            background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
            border: none;
            color: white;
            font-size: 22px;
            cursor: pointer;
            padding: 10px 14px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header-card {
                padding: 20px;
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .mobile-menu-btn {
                display: block;
            }
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 999;
            backdrop-filter: blur(4px);
        }

        @media (max-width: 768px) {
            .sidebar-overlay.active {
                display: block;
            }
        }

        /* Activity Indicator */
        .activity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .activity-card {
            background: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .activity-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .activity-info h4 {
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 4px;
            font-weight: 500;
        }

        .activity-info p {
            font-size: 24px;
            font-weight: 700;
            color: #1F2937;
        }

        .lessons-section {
            background: white;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileMenu()"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-text">CodeLearn</div>
            <button class="toggle-btn" onclick="toggleSidebar()">
                <span id="toggleIcon">‹</span>
            </button>
        </div>
        <nav class="sidebar-menu">
            <a href="#" class="menu-item active" onclick="showSection('dashboard', this); return false;">
                <span class="menu-icon"><i class="fas fa-chart-line"></i></span>
                <span class="menu-text">Dashboard</span>
            </a>
            <a href="./admin_courses.php" class="menu-item" onclick="showSection('courses', this); return false;">
                <span class="menu-icon"><i class="fas fa-book"></i></span>
                <span class="menu-text">Courses</span>
            </a>
            <!-- <a href="./add-lessons.php" class="menu-item" onclick="showSection('lessons', this); return false;">
                <span class="menu-icon"><i class="fas fa-graduation-cap"></i></span>
                <span class="menu-text">Lessons</span>
            </a> -->
            <a href="#" class="menu-item" onclick="showSection('quiz', this); return false;">
                <span class="menu-icon"><i class="fas fa-question-circle"></i></span>
                <span class="menu-text">Quiz Management</span>
            </a>
            <a href="#" class="menu-item" onclick="showSection('users', this); return false;">
                <span class="menu-icon"><i class="fas fa-users"></i></span>
                <span class="menu-text">Users</span>
            </a>
            <a href="#" class="menu-item" onclick="showSection('certificates', this); return false;">
                <span class="menu-icon"><i class="fas fa-certificate"></i></span>
                <span class="menu-text">Certificates</span>
            </a>
            <a href="#" class="menu-item" onclick="showSection('feedback', this); return false;">
                <span class="menu-icon"><i class="fas fa-comments"></i></span>
                <span class="menu-text">Feedback</span>
            </a>
            <a href="#" class="menu-item" onclick="showSection('payments', this); return false;">
                <span class="menu-icon"><i class="fas fa-credit-card"></i></span>
                <span class="menu-text">Payments</span>
            </a>
            <!-- <a href="#" class="menu-item" onclick="showSection('forgot-password', this); return false;">
                <span class="menu-icon"><i class="fas fa-key"></i></span>
                <span class="menu-text">Reset Password</span>
            </a> -->
            <a href="#" class="menu-item" onclick="showSection('profile-settings', this); return false;">
                <span class="menu-icon"><i class="fas fa-user-cog"></i></span>
                <span class="menu-text">Profile Settings</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header Card -->
        <div class="header-card">
            <div style="display: flex; align-items: center; gap: 20px;">
                <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
                <h1 class="header-title" id="pageTitle">Dashboard</h1>
            </div>
            
            <div class="user-info">
                <div class="user-avatar"><?php echo $admin_initial; ?></div>
                <div class="user-details">
                    <h3><?php echo htmlspecialchars($admin_name); ?></h3>
                    <p>Administrator</p>
                    <button class="logout-btn">Logout</button>
                </div>
            </div>
        </div>

        <!-- Content Sections -->
        <!-- Dashboard Section -->
        <div class="content active" id="dashboard">
            <!-- Live Activity -->
            <div class="activity-grid">
                <div class="activity-card">
                    <div class="activity-icon-wrapper" style="background: rgba(16, 185, 129, 0.15); color: #10B981;">
                        🟢
                    </div>
                    <div class="activity-info">
                        <h4>Live Users</h4>
                        <p><?php echo $stats['online_users']; ?></p>
                    </div>
                </div>
                
                <div class="activity-card">
                    <div class="activity-icon-wrapper" style="background: rgba(59, 130, 246, 0.15); color: #3B82F6;">
                        👤
                    </div>
                    <div class="activity-info">
                        <h4>Today's Signups</h4>
                        <p><?php echo $stats['today_users']; ?></p>
                    </div>
                </div>
                
                <div class="activity-card">
                    <div class="activity-icon-wrapper" style="background: rgba(139, 92, 246, 0.15); color: #8B5CF6;">
                        📈
                    </div>
                    <div class="activity-info">
                        <h4>This Month</h4>
                        <p><?php echo $stats['month_users']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-content">
                        <div class="stat-info">
                            <h2><?php echo number_format($stats['total_users']); ?></h2>
                            <p>Total Students</p>
                        </div>
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                    </div>
                    <div class="stat-growth <?php echo $growth_percentage < 0 ? 'negative' : ''; ?>">
                        <?php echo $growth_percentage >= 0 ? '↑' : '↓'; ?> <?php echo abs($growth_percentage); ?>% from last month
                    </div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-content">
                        <div class="stat-info">
                            <h2>₹<?php echo number_format($stats['month_revenue'], 2); ?></h2>
                            <p>Revenue This Month</p>
                        </div>
                        <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                    </div>
                    <div class="stat-growth <?php echo $stats['revenue_growth'] < 0 ? 'negative' : ''; ?>">
                        <?php echo $stats['revenue_growth'] >= 0 ? '↑' : '↓'; ?> <?php echo abs($stats['revenue_growth']); ?>% from last month
                    </div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-content">
                        <div class="stat-info">
                            <h2><?php echo number_format($stats['total_feedback']); ?></h2>
                            <p>Total Feedback</p>
                        </div>
                        <div class="stat-icon"><i class="fas fa-comment-dots"></i></div>
                    </div>
                    <div class="stat-growth">
                        <?php if ($stats['avg_rating'] > 0): ?>
                            ★ <?php echo $stats['avg_rating']; ?> avg rating
                        <?php else: ?>
                            No feedback yet
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-content">
                    <div class="stat-info">
                        <h2><?php echo number_format($stats['total_payments']); ?></h2>
                        <p>Total Transactions</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-credit-card"></i></div>
                </div>
                <div class="stat-growth">
                    ₹<?php echo number_format($stats['total_revenue'], 2); ?> total revenue
                </div>
            </div>

            <!-- User Registration Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h2 class="chart-title">User Registration Trend (Last 7 Days)</h2>
                    <div class="live-badge">
                        <span class="live-dot"></span>
                        Live Data
                    </div>
                </div>
                <canvas id="userChart" style="max-height: 300px;"></canvas>
            </div>

            <!-- Recent Users Table -->
            <!-- Recent Users Table -->
<div class="data-table">
    <div class="table-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 class="table-title">Recent User Registrations</h2>
        <div style="display: flex; gap: 12px; align-items: center;">
            <select id="dashboardPlanFilter" onchange="filterDashboardUsers()" style="padding: 10px 16px; border: 1px solid #E5E7EB; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; background: white;">
                <option value="all">All Plans</option>
                <option value="free">Free Plan</option>
                <option value="pro">Pro Plan</option>
                <option value="team">Max Plan</option>
            </select>
            <span class="live-badge">
                <span class="live-dot"></span>
                <?php echo $stats['today_users']; ?> today
            </span>
        </div>
    </div>
    <?php if (count($recent_users) > 0): ?>
    <table class="recent-users-table" id="dashboardUsersTable">
        <thead>
            <tr>
                <th>Avatar</th>
                <th>Name</th>
                <th>Email</th>
                <th>Plan</th>
                <th>Joined Date</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Update the query to include plan
            $stmt = $pdo->query("SELECT name, email, plan, created_at FROM users ORDER BY created_at DESC LIMIT 10");
            $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($recent_users as $user): 
                $userPlan = $user['plan'] ?? 'free';
                $planColors = [
                    'free' => '#6B7280',
                    'pro' => '#3B82F6',
                    'team' => '#8B5CF6'
                ];
                $planColor = $planColors[$userPlan] ?? '#6B7280';
            ?>
            <tr data-plan="<?php echo htmlspecialchars($userPlan); ?>">
                <td>
                    <div class="user-avatar-small">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                </td>
                <td style="font-weight: 600;"><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                    <span style="background: <?php echo $planColor; ?>; color: white; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; text-transform: uppercase; display: inline-block;">
                        <?php echo htmlspecialchars($userPlan); ?>
                    </span>
                </td>
                <td><?php echo date('M d, Y H:i', strtotime($user['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon">👥</div>
        <h3>No Users Registered Yet</h3>
        <p>New user registrations will appear here</p>
    </div>
    <?php endif; ?>
</div>
        </div>
        
        <!-- Payments Section -->
         
    <div class="content" id="payments">
    <!-- Payment Stats Overview -->
    <div class="stats-grid" style="margin-bottom: 30px;">
        <div class="stat-card blue">
            <div class="stat-content">
                <div class="stat-info">
                    <h2>₹<?php echo number_format($stats['total_revenue'], 2); ?></h2>
                    <p>Total Revenue</p>
                </div>
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            </div>
            <div class="stat-growth"><?php echo $stats['total_payments']; ?> transactions</div>
        </div>
        
        <div class="stat-card purple">
            <div class="stat-content">
                <div class="stat-info">
                    <h2><?php echo $stats['pro_users']; ?></h2>
                    <p>Pro Plan Users</p>
                </div>
                <div class="stat-icon"><i class="fas fa-crown"></i></div>
            </div>
            <div class="stat-growth">Active subscriptions</div>
        </div>
        
        <div class="stat-card green">
            <div class="stat-content">
                <div class="stat-info">
                    <h2><?php echo $stats['team_users']; ?></h2>
                    <p>Max Plan Users</p>
                </div>
                <div class="stat-icon"><i class="fas fa-star"></i></div>
            </div>
            <div class="stat-growth">Premium members</div>
        </div>
    </div>

    <!-- ADD THIS - Transaction Chart -->
<div class="chart-container" style="margin-top: 30px;">
    <div class="table-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h2 class="table-title">Recent Transactions</h2>
    <div style="display: flex; gap: 12px; align-items: center;">
        <select id="paymentPlanFilter" onchange="filterPayments()" style="padding: 10px 16px; border: 1px solid #E5E7EB; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; background: white;">
            <option value="all">All Plans</option>
            <option value="pro">Pro Plan</option>
            <option value="team">Max Plan</option>
        </select>
        <span class="live-badge">
            <span class="live-dot"></span>
            Live
        </span>
    </div>
</div>
    <canvas id="transactionChart" style="max-height: 320px;"></canvas>
</div>

    <!-- Payment Transactions Table -->
    <div class="data-table">
        <div class="table-header">
            <h2 class="table-title">Recent Transactions</h2>
            <span class="live-badge">
                <span class="live-dot"></span>
                Live
            </span>
        </div>
        <?php 
        $stmt = $pdo->query("
            SELECT p.*, u.name, u.email 
            FROM payment p 
            JOIN users u ON p.user_id = u.user_id 
            WHERE p.status = 'completed'
            ORDER BY p.payment_date DESC 
            LIMIT 50
        ");
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($payments) > 0): ?>
            <table class="recent-users-table" id="paymentsTable">
                <thead>
                <tr>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Transaction ID</th>
                    <th>Date</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr data-plan="<?php echo htmlspecialchars($payment['plan']); ?>">
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="user-avatar-small">
                                <?php echo strtoupper(substr($payment['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <div style="font-weight: 600;"><?php echo htmlspecialchars($payment['name']); ?></div>
                                <div style="font-size: 12px; color: #9CA3AF;"><?php echo htmlspecialchars($payment['email']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="background: <?php 
                            echo $payment['plan'] === 'team' ? '#8B5CF6' : ($payment['plan'] === 'pro' ? '#3B82F6' : '#6B7280'); 
                        ?>; color: white; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; text-transform: uppercase;">
                            <?php echo $payment['plan']; ?>
                        </span>
                    </td>
                    <td style="font-weight: 700; color: #10B981;">₹<?php echo number_format($payment['amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                    <td style="font-family: monospace; font-size: 12px; color: #6B7280;"><?php echo substr($payment['transaction_id'], 0, 20); ?>...</td>
                    <td><?php echo date('M d, Y', strtotime($payment['payment_date'])); ?></td>
                    <td><?php echo $payment['duration_months']; ?> month<?php echo $payment['duration_months'] > 1 ? 's' : ''; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">💳</div>
            <h3>No Transactions Yet</h3>
            <p>Payment transactions will appear here once users upgrade</p>
        </div>
        <?php endif; ?>
    </div>
</div>




        <!-- Courses Section -->
        <div class="content" id="courses">
            <iframe src="admin_courses.php" style="width: 100%; height: calc(100vh - 200px); border: none; border-radius: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);"></iframe>
        </div>

        <!-- Lessons Section -->
        <div class="content" id="lessons">
            <div class="data-table">
                <div class="table-header">
                    <h2 class="table-title">Lessons Management</h2>
                </div>
                <div class="empty-state">
                    <div class="empty-state-icon">📖</div>
                    <h3>Lessons Page Coming Soon</h3>
                    <p>Lesson management features will be added here</p>
                </div>
            </div>
        </div>

        <!-- Quiz Section -->
        <!-- Quiz Section -->
        <div class="content" id="quiz">
            <iframe src="./admin_quizz.php" style="width: 100%; height: calc(100vh - 200px); border: none; border-radius: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);"></iframe>
        </div>
        <!-- Users Section -->
        <!-- Users Section -->
<div class="content" id="users">
    <div class="data-table">
        <div class="table-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="table-title">Users Management</h2>
            <div style="display: flex; gap: 12px;">
                <select id="planFilter" onchange="filterUsers()" style="padding: 10px 16px; border: 1px solid #E5E7EB; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; background: white;">
                    <option value="all">All Plans</option>
                    <option value="free">Free Plan</option>
                    <option value="pro">Pro Plan</option>
                    <option value="team">Max Plan</option>
                </select>
            </div>
        </div>
        <?php if ($stats['total_users'] > 0): ?>
        <table class="recent-users-table" id="usersTable">
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Plan</th>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $stmt = $pdo->query("SELECT user_id, name, email, plan, created_at FROM users ORDER BY created_at DESC");
                $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($all_users as $user): 
                    $userPlan = $user['plan'] ?? 'free';
                    $planColors = [
                        'free' => '#6B7280',
                        'pro' => '#3B82F6',
                        'team' => '#8B5CF6'
                    ];
                    $planColor = $planColors[$userPlan] ?? '#6B7280';
                ?>
                <tr data-plan="<?php echo htmlspecialchars($userPlan); ?>">
                    <td>
                        <div class="user-avatar-small">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                    </td>
                    <td style="font-weight: 600;"><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td>
                        <span style="background: <?php echo $planColor; ?>; color: white; padding: 6px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; text-transform: uppercase; display: inline-block;">
                            <?php echo htmlspecialchars($userPlan); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y H:i', strtotime($user['created_at'])); ?></td>
                    <td>
                        <button onclick="deleteUser(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>')" style="background: linear-gradient(135deg, #EF4444, #DC2626); color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <h3>No Users Registered</h3>
            <p>User accounts will appear here once they sign up</p>
        </div>
        <?php endif; ?>
    </div>
</div>

        <!-- Certificates Section -->
        <div class="content" id="certificates">
            <iframe src="./admin_certificate.php" style="width: 100%; height: calc(100vh - 200px); border: none; border-radius: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);"></iframe>
        </div>

        <!-- Feedback Section -->
        <div class="content" id="feedback">
            <iframe src="admin_feedback.php" style="width: 100%; height: calc(100vh - 200px); border: none; border-radius: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);"></iframe>
        </div>

           

        <!-- Forgot Password Section -->


        <!-- Profile Settings Section -->
        <div class="content" id="profile-settings">
            <iframe src="admin_profile_settings.php" style="width: 100%; height: calc(100vh - 200px); border: none; border-radius: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);"></iframe>
        </div>
    </div>
<script>
// Transaction Chart Data
<?php
// Get transaction data for last 7 days
$stmt = $pdo->query("
    SELECT DATE(payment_date) as date, COUNT(*) as count, SUM(amount) as total_amount
    FROM payment 
    WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND status = 'completed'
    GROUP BY DATE(payment_date)
    ORDER BY date ASC
");
$transaction_chart_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fill missing dates with zero
$last_7_days_transactions = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $found = false;
    foreach ($transaction_chart_data as $data) {
        if ($data['date'] === $date) {
            $last_7_days_transactions[] = [
                'date' => $date, 
                'count' => $data['count'],
                'amount' => $data['total_amount']
            ];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $last_7_days_transactions[] = ['date' => $date, 'count' => 0, 'amount' => 0];
    }
}
?>

const transactionChartData = <?php echo json_encode($last_7_days_transactions); ?>;
const transactionLabels = transactionChartData.map(item => {
    const date = new Date(item.date);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
});
const transactionCounts = transactionChartData.map(item => item.count);
const transactionAmounts = transactionChartData.map(item => parseFloat(item.amount));

const ctxTransaction = document.getElementById('transactionChart').getContext('2d');

// Gradient for transaction count
const gradientCount = ctxTransaction.createLinearGradient(0, 0, 0, 300);
gradientCount.addColorStop(0, 'rgba(139, 92, 246, 0.4)');
gradientCount.addColorStop(1, 'rgba(139, 92, 246, 0.0)');

// Gradient for amount
const gradientAmount = ctxTransaction.createLinearGradient(0, 0, 0, 300);
gradientAmount.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
gradientAmount.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

const transactionChart = new Chart(ctxTransaction, {
    type: 'line',
    data: {
        labels: transactionLabels,
        datasets: [
            {
                label: 'Transactions Count',
                data: transactionCounts,
                backgroundColor: gradientCount,
                borderColor: '#8B5CF6',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#8B5CF6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
                yAxisID: 'y'
            },
            {
                label: 'Revenue (₹)',
                data: transactionAmounts,
                backgroundColor: gradientAmount,
                borderColor: '#10B981',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10B981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    padding: 20,
                    font: {
                        size: 13,
                        weight: '600'
                    },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(30, 41, 59, 0.95)',
                padding: 14,
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                },
                borderColor: '#8B5CF6',
                borderWidth: 1,
                cornerRadius: 10,
                displayColors: true,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            if (context.datasetIndex === 1) {
                                label += '₹' + context.parsed.y.toLocaleString('en-IN', {maximumFractionDigits: 2});
                            } else {
                                label += context.parsed.y;
                            }
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    color: '#8B5CF6',
                    font: {
                        size: 12,
                        weight: '600'
                    }
                },
                grid: {
                    color: 'rgba(139, 92, 246, 0.1)',
                    drawBorder: false
                },
                title: {
                    display: true,
                    text: 'Transactions',
                    color: '#8B5CF6',
                    font: {
                        size: 13,
                        weight: '700'
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                ticks: {
                    color: '#10B981',
                    font: {
                        size: 12,
                        weight: '600'
                    },
                    callback: function(value) {
                        return '₹' + value.toLocaleString('en-IN');
                    }
                },
                grid: {
                    drawOnChartArea: false,
                    drawBorder: false
                },
                title: {
                    display: true,
                    text: 'Revenue',
                    color: '#10B981',
                    font: {
                        size: 13,
                        weight: '700'
                    }
                }
            },
            x: {
                ticks: {
                    color: '#9CA3AF',
                    font: {
                        size: 12
                    }
                },
                grid: {
                    display: false,
                    drawBorder: false
                }
            }
        }
    }
});
</script>
    <script>
        // Chart.js Configuration
        const chartData = <?php echo json_encode($last_7_days); ?>;
        const labels = chartData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        const data = chartData.map(item => item.count);

        const ctx = document.getElementById('userChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        const userChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'User Registrations',
                    data: data,
                    backgroundColor: gradient,
                    borderColor: '#3B82F6',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3B82F6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 41, 59, 0.95)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        borderColor: '#3B82F6',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#9CA3AF',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            color: '#9CA3AF',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            toggleIcon.textContent = sidebar.classList.contains('collapsed') ? '›' : '‹';
        }

        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.add('mobile-open');
            overlay.classList.add('active');
        }

        function closeMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        }

        function showSection(sectionId, element) {
            // Hide all sections
            const sections = document.querySelectorAll('.content');
            sections.forEach(section => section.classList.remove('active'));
            
            // Show selected section
            document.getElementById(sectionId).classList.add('active');
            
            // Update active menu item
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => item.classList.remove('active'));
            element.classList.add('active');
            
            // Update page title
            const titles = {
                'dashboard': 'Dashboard',
                'courses': 'Courses',
                'lessons': 'Lessons',
                'quiz': 'Quiz Management',
                'users': 'Users',
                'certificates': 'Certificates',
                'feedback': 'Feedback',
                'payments': 'Payments',
                'forgot-password': 'Reset Password',
                'profile-settings': 'Profile Settings'
            };
            
            document.getElementById('pageTitle').textContent = titles[sectionId] || 'Dashboard';
            
            // Close mobile menu
            closeMobileMenu();
        }

        // ADMIN LOGOUT FUNCTION
        function logout() {
            console.log('Admin logout function called');
            showAdminLogoutConfirmation('Admin Logout', 'Are you sure you want to logout from admin panel?');
        }

        function showAdminLogoutConfirmation(title, message) {
            console.log('Showing admin logout confirmation');
            
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(10px);
                display: flex; align-items: center; justify-content: center;
                z-index: 10000; opacity: 0; transition: all 0.3s ease;
            `;

            const modalContent = document.createElement('div');
            modalContent.innerHTML = `
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                    <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3B82F6, #8B5CF6); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);">🔐</div>
                    <div>
                        <h3 style="margin: 0; color: #1f2937; font-size: 20px; font-weight: 700;">Logout Confirmation</h3>
                        <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 14px;">Thank you for managing CodeLearn</p>
                    </div>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button id="cancelBtn" style="flex: 1; padding: 14px; border: 1px solid #e5e7eb; background: white; color: #374151; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease;">Cancel</button>
                    <button id="confirmBtn" style="flex: 1; padding: 14px; border: none; background: linear-gradient(135deg, #3B82F6, #8B5CF6); color: white; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">Logout</button>
                </div>
            `;
            modalContent.style.cssText = `
                background: white; padding: 32px; border-radius: 20px; max-width: 400px; width: 90%;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                transform: translateY(20px) scale(0.95); transition: all 0.3s ease;
            `;

            modal.appendChild(modalContent);
            document.body.appendChild(modal);
            document.body.style.overflow = 'hidden';

            // Cancel button
            document.getElementById('cancelBtn').onclick = function() {
                closeModal();
            };

            // Confirm button
            document.getElementById('confirmBtn').onclick = function() {
                this.innerHTML = '<div style="width: 18px; height: 18px; border: 2px solid white; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>';
                this.disabled = true;
                
                fetch('logout.php', { method: 'POST' })
                .then(() => {
                    closeModal();
                    showAdminLogoutToast();
                    setTimeout(() => window.location.href = 'index.php', 2000);
                })
                .catch(() => {
                    window.location.href = 'logout.php';
                });
            };

            // Close modal function
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

            // Click outside to close
            modal.onclick = function(e) {
                if (e.target === modal) closeModal();
            };

            // Add spinner animation
            if (!document.getElementById('admin-spinner-style')) {
                const style = document.createElement('style');
                style.id = 'admin-spinner-style';
                style.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
                document.head.appendChild(style);
            }

            // Animate in
            setTimeout(() => {
                modal.style.opacity = '1';
                modalContent.style.transform = 'translateY(0) scale(1)';
            }, 10);
        }

        function showAdminLogoutToast() {
            const toast = document.createElement('div');
            toast.innerHTML = `
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #3B82F6, #8B5CF6); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">✓</div>
                    <div>
                        <div style="font-weight: 700; font-size: 15px; color: #111827;">Logout successful!</div>
                        <div style="color: #6b7280; font-size: 13px; margin-top: 2px;">Thanks for managing CodeLearn</div>
                    </div>
                </div>
            `;
            toast.style.cssText = `
                position: fixed; top: 24px; right: 24px; background: white; 
                padding: 18px 22px; border-radius: 14px; 
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                z-index: 10001; transform: translateX(400px); transition: all 0.4s ease;
                border: 1px solid #E5E7EB; min-width: 300px;
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

        // Auto refresh dashboard data every 30 seconds
        setInterval(function() {
            if (document.getElementById('dashboard').classList.contains('active')) {
                location.reload();
            }
        }, 30000);

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeMobileMenu();
            }
        });

        // Logout button event listener
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.querySelector('.logout-btn');
            if (logoutBtn) {
                logoutBtn.removeAttribute('onclick');
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Admin logout button clicked');
                    logout();
                });
            }
        });

        // User Filter Function
function filterUsers() {
    const filterValue = document.getElementById('planFilter').value;
    const rows = document.querySelectorAll('#usersTable tbody tr');
    
    rows.forEach(row => {
        const planValue = row.getAttribute('data-plan') || 'free';
        if (filterValue === 'all' || planValue === filterValue) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Delete User Function
function deleteUser(userId, userName) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(10px);
        display: flex; align-items: center; justify-content: center;
        z-index: 10000; opacity: 0; transition: all 0.3s ease;
    `;

    const modalContent = document.createElement('div');
    modalContent.innerHTML = `
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #EF4444, #DC2626); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);">⚠️</div>
            <div>
                <h3 style="margin: 0; color: #1f2937; font-size: 20px; font-weight: 700;">Delete User</h3>
                <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 14px;">This action cannot be undone</p>
            </div>
        </div>
        <p style="color: #4B5563; margin-bottom: 24px; font-size: 15px;">Are you sure you want to delete <strong style="color: #1F2937;">${userName}</strong>? All related data will be permanently removed.</p>
        <div style="display: flex; gap: 12px;">
            <button id="cancelBtn" style="flex: 1; padding: 14px; border: 1px solid #e5e7eb; background: white; color: #374151; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease;">Cancel</button>
            <button id="confirmBtn" style="flex: 1; padding: 14px; border: none; background: linear-gradient(135deg, #EF4444, #DC2626); color: white; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">Delete User</button>
        </div>
    `;
    modalContent.style.cssText = `
        background: white; padding: 32px; border-radius: 20px; max-width: 420px; width: 90%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        transform: translateY(20px) scale(0.95); transition: all 0.3s ease;
    `;

    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';

    document.getElementById('cancelBtn').onclick = () => closeModal();
    
    document.getElementById('confirmBtn').onclick = function() {
        this.innerHTML = '<div style="width: 18px; height: 18px; border: 2px solid white; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>';
        this.disabled = true;
        
        fetch('delete_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `user_id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                showToast('Success', 'User deleted successfully', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                closeModal();
                showToast('Error', data.message || 'Failed to delete user', 'error');
            }
        })
        .catch(error => {
            closeModal();
            showToast('Error', 'Network error occurred', 'error');
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

    modal.onclick = (e) => { if (e.target === modal) closeModal(); };
    
    setTimeout(() => {
        modal.style.opacity = '1';
        modalContent.style.transform = 'translateY(0) scale(1)';
    }, 10);
}

// Payment Filter Function
function filterPayments() {
    const filterValue = document.getElementById('paymentPlanFilter').value;
    const rows = document.querySelectorAll('#paymentsTable tbody tr');
    
    rows.forEach(row => {
        const planValue = row.getAttribute('data-plan');
        if (filterValue === 'all' || planValue === filterValue) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Toast Notification Function
function showToast(title, message, type = 'success') {
    const colors = {
        success: { bg: '#10B981', icon: '✓' },
        error: { bg: '#EF4444', icon: '✕' }
    };
    
    const toast = document.createElement('div');
    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; background: ${colors[type].bg}; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; font-weight: 700; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);">${colors[type].icon}</div>
            <div>
                <div style="font-weight: 700; font-size: 15px; color: #111827;">${title}</div>
                <div style="color: #6b7280; font-size: 13px; margin-top: 2px;">${message}</div>
            </div>
        </div>
    `;
    toast.style.cssText = `
        position: fixed; top: 24px; right: 24px; background: white; 
        padding: 18px 22px; border-radius: 14px; 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 10001; transform: translateX(400px); transition: all 0.4s ease;
        border: 1px solid #E5E7EB; min-width: 300px;
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
    }, 3000);
}
// Dashboard Users Filter Function
function filterDashboardUsers() {
    const filterValue = document.getElementById('dashboardPlanFilter').value;
    const rows = document.querySelectorAll('#dashboardUsersTable tbody tr');
    
    rows.forEach(row => {
        const planValue = row.getAttribute('data-plan') || 'free';
        if (filterValue === 'all' || planValue === filterValue) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
    </script>
</body>
</html>