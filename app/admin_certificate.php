<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php');
    exit();
}

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

// Get certificate statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_certificates FROM certificates");
$total_certificates = $stmt->fetch()['total_certificates'];

$stmt = $pdo->query("SELECT COUNT(*) as today_certificates FROM certificates WHERE DATE(issued_date) = CURDATE()");
$today_certificates = $stmt->fetch()['today_certificates'];

$stmt = $pdo->query("SELECT COUNT(*) as month_certificates FROM certificates WHERE MONTH(issued_date) = MONTH(CURRENT_DATE()) AND YEAR(issued_date) = YEAR(CURRENT_DATE())");
$month_certificates = $stmt->fetch()['month_certificates'];

// Get all certificates with user and course details
$stmt = $pdo->query("
    SELECT 
        c.certificate_id,
        c.certificate_number,
        c.issued_date,
        c.course_id,
        u.user_id,
        u.name as user_name,
        u.email as user_email,
        u.plan as user_plan,
        co.title as course_title
    FROM certificates c
    JOIN users u ON c.user_id = u.user_id
    JOIN courses co ON c.course_id = co.course_id
    ORDER BY c.issued_date DESC
");
$certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get course-wise certificate count
$stmt = $pdo->query("
    SELECT 
        co.title as course_name,
        COUNT(c.certificate_id) as count
    FROM certificates c
    JOIN courses co ON c.course_id = co.course_id
    GROUP BY c.course_id, co.title
    ORDER BY count DESC
    LIMIT 5
");
$course_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificates Management - CodeLearn Admin</title>
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
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 28px 36px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #1E293B 0%, #8B5CF6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }

        .header p {
            color: #6B7280;
            font-size: 14px;
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
            border: 1px solid #E5E7EB;
            position: relative;
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

        .stat-card.purple { --card-color: #8B5CF6; --card-color-light: #A78BFA; }
        .stat-card.blue { --card-color: #3B82F6; --card-color-light: #60A5FA; }
        .stat-card.green { --card-color: #10B981; --card-color-light: #34D399; }

        .stat-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-info h2 {
            font-size: 36px;
            font-weight: 800;
            color: #1F2937;
            margin-bottom: 6px;
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

        /* Filters */
        .filters-bar {
            background: white;
            padding: 20px 24px;
            border-radius: 16px;
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-group label {
            font-size: 14px;
            font-weight: 600;
            color: #4B5563;
        }

        .filter-select {
            padding: 10px 16px;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            background: white;
            color: #374151;
            transition: all 0.2s;
        }

        .filter-select:hover {
            border-color: #8B5CF6;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #8B5CF6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
        }

        /* Certificates Table */
        .certificates-table {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #FAFBFC;
        }

        th {
            padding: 18px 20px;
            text-align: left;
            color: #6B7280;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #F3F4F6;
        }

        td {
            padding: 18px 20px;
            color: #4B5563;
            font-size: 14px;
            border-bottom: 1px solid #F3F4F6;
        }

        tbody tr {
            transition: all 0.2s;
        }

        tbody tr:hover {
            background: #FAFBFC;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
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
            flex-shrink: 0;
        }

        .user-details {
            min-width: 0;
        }

        .user-name {
            font-weight: 600;
            color: #1F2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-email {
            font-size: 12px;
            color: #9CA3AF;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .plan-badge {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .plan-free {
            background: #F3F4F6;
            color: #6B7280;
        }

        .plan-pro {
            background: rgba(59, 130, 246, 0.1);
            color: #3B82F6;
        }

        .plan-team {
            background: rgba(139, 92, 246, 0.1);
            color: #8B5CF6;
        }

        .cert-number {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #6B7280;
            font-weight: 600;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-view {
            background: linear-gradient(135deg, #3B82F6, #8B5CF6);
            color: white;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .btn-delete {
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: white;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

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

        /* Top Courses Section */
        .top-courses {
            background: white;
            padding: 28px;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
            margin-bottom: 30px;
        }

        .top-courses h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 20px;
        }

        .course-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #F3F4F6;
        }

        .course-item:last-child {
            border-bottom: none;
        }

        .course-name {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .course-count {
            background: linear-gradient(135deg, #8B5CF6, #A78BFA);
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .filters-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-certificate"></i> Certificates Management</h1>
            <p>Manage and track all issued certificates</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card purple">
                <div class="stat-content">
                    <div class="stat-info">
                        <h2><?php echo number_format($total_certificates); ?></h2>
                        <p>Total Certificates</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-certificate"></i></div>
                </div>
            </div>

            <div class="stat-card blue">
                <div class="stat-content">
                    <div class="stat-info">
                        <h2><?php echo number_format($today_certificates); ?></h2>
                        <p>Issued Today</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-content">
                    <div class="stat-info">
                        <h2><?php echo number_format($month_certificates); ?></h2>
                        <p>This Month</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>

        <!-- Top Courses -->
        <?php if (count($course_stats) > 0): ?>
        <div class="top-courses">
            <h3><i class="fas fa-trophy"></i> Top Courses by Certificates</h3>
            <?php foreach ($course_stats as $course): ?>
            <div class="course-item">
                <span class="course-name"><?php echo htmlspecialchars($course['course_name']); ?></span>
                <span class="course-count"><?php echo $course['count']; ?> certificates</span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filters-bar">
            <div class="filter-group">
                <label><i class="fas fa-filter"></i> Plan:</label>
                <select class="filter-select" id="planFilter" onchange="filterCertificates()">
                    <option value="all">All Plans</option>
                    <option value="free">Free</option>
                    <option value="pro">Pro</option>
                    <option value="team">Max</option>
                </select>
            </div>

            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search by name, email, or certificate number..." onkeyup="searchCertificates()">
            </div>
        </div>

        <!-- Certificates Table -->
        <div class="certificates-table">
            <?php if (count($certificates) > 0): ?>
            <div class="table-container">
                <table id="certificatesTable">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Course Title</th>
                            <th>Plan</th>
                            <th>Certificate Number</th>
                            <th>Course ID</th>
                            <th>Issue Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certificates as $cert): ?>
                        <tr data-plan="<?php echo htmlspecialchars($cert['user_plan']); ?>" 
                            data-search="<?php echo strtolower(htmlspecialchars($cert['user_name'] . ' ' . $cert['user_email'] . ' ' . $cert['certificate_number'])); ?>">
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($cert['user_name'], 0, 1)); ?>
                                    </div>
                                    <div class="user-details">
                                        <div class="user-name"><?php echo htmlspecialchars($cert['user_name']); ?></div>
                                        <div class="user-email"><?php echo htmlspecialchars($cert['user_email']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($cert['course_title']); ?></td>
                            <td>
                                <span class="plan-badge plan-<?php echo htmlspecialchars($cert['user_plan']); ?>">
                                    <?php echo htmlspecialchars($cert['user_plan']); ?>
                                </span>
                            </td>
                            <td class="cert-number"><?php echo htmlspecialchars($cert['certificate_number']); ?></td>
                            <td style="font-family: monospace; color: #6B7280;">#<?php echo $cert['course_id']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($cert['issued_date'])); ?></td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <!-- <a href="view_certificate.php?course_id=<?php echo $cert['course_id']; ?>" target="_blank" class="action-btn btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a> -->
                                    <button onclick="deleteCertificate(<?php echo $cert['certificate_id']; ?>, '<?php echo htmlspecialchars($cert['user_name'], ENT_QUOTES); ?>')" class="action-btn btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">🎓</div>
                <h3>No Certificates Issued Yet</h3>
                <p>Certificates will appear here once users complete courses</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Filter certificates by plan
        function filterCertificates() {
            const filterValue = document.getElementById('planFilter').value;
            const rows = document.querySelectorAll('#certificatesTable tbody tr');
            
            rows.forEach(row => {
                const plan = row.getAttribute('data-plan');
                if (filterValue === 'all' || plan === filterValue) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Search certificates
        function searchCertificates() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#certificatesTable tbody tr');
            
            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                if (searchData.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Delete certificate
        function deleteCertificate(certId, userName) {
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
                    <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #EF4444, #DC2626); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">⚠️</div>
                    <div>
                        <h3 style="margin: 0; color: #1f2937; font-size: 20px; font-weight: 700;">Delete Certificate</h3>
                        <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 14px;">This action cannot be undone</p>
                    </div>
                </div>
                <p style="color: #4B5563; margin-bottom: 24px;">Are you sure you want to delete the certificate for <strong>${userName}</strong>?</p>
                <div style="display: flex; gap: 12px;">
                    <button id="cancelBtn" style="flex: 1; padding: 14px; border: 1px solid #e5e7eb; background: white; color: #374151; border-radius: 10px; cursor: pointer; font-weight: 600;">Cancel</button>
                    <button id="confirmBtn" style="flex: 1; padding: 14px; border: none; background: linear-gradient(135deg, #EF4444, #DC2626); color: white; border-radius: 10px; cursor: pointer; font-weight: 600;">Delete</button>
                </div>
            `;
            modalContent.style.cssText = `
                background: white; padding: 32px; border-radius: 20px; max-width: 420px; width: 90%;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                transform: translateY(20px) scale(0.95); transition: all 0.3s ease;
            `;

            modal.appendChild(modalContent);
            document.body.appendChild(modal);

            document.getElementById('cancelBtn').onclick = () => closeModal();
            
            document.getElementById('confirmBtn').onclick = function() {
                this.innerHTML = '<div style="width: 18px; height: 18px; border: 2px solid white; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>';
                this.disabled = true;
                
                fetch('delete_certificate.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `certificate_id=${certId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal();
                        showToast('Success', 'Certificate deleted successfully', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        closeModal();
                        showToast('Error', data.message || 'Failed to delete', 'error');
                    }
                })
                .catch(() => {
                    closeModal();
                    showToast('Error', 'Network error occurred', 'error');
                });
            };

            function closeModal() {
                modal.style.opacity = '0';
                setTimeout(() => document.body.removeChild(modal), 300);
            }

            modal.onclick = (e) => { if (e.target === modal) closeModal(); };
            setTimeout(() => modal.style.opacity = '1', 10);
        }

        function showToast(title, message, type = 'success') {
            const colors = {
                success: { bg: '#10B981', icon: '✓' },
                error: { bg: '#EF4444', icon: '✕' }
            };
            
            const toast = document.createElement('div');
            toast.innerHTML = `
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; background: ${colors[type].bg}; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">${colors[type].icon}</div>
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
            `;

            document.body.appendChild(toast);
            setTimeout(() => toast.style.transform = 'translateX(0)', 100);
            setTimeout(() => {
                toast.style.transform = 'translateX(400px)';
                setTimeout(() => document.body.removeChild(toast), 400);
            }, 3000);
        }
    </script>
</body>
</html>