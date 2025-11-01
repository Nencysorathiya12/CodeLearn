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

// Handle delete feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_feedback'])) {
    $feedback_id = intval($_POST['feedback_id']);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM feedback WHERE feedback_id = ?");
        $stmt->execute([$feedback_id]);
        $_SESSION['success_message'] = "Feedback deleted successfully!";
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Failed to delete feedback: " . $e->getMessage();
    }
    
    header('Location: admin_feedback.php');
    exit();
}

// Get feedback statistics
$stats = [];

// Total feedback count
$stmt = $pdo->query("SELECT COUNT(*) as total_feedback FROM feedback");
$stats['total_feedback'] = $stmt->fetch()['total_feedback'];

// Feedback today
$stmt = $pdo->query("SELECT COUNT(*) as today_feedback FROM feedback WHERE DATE(created_at) = CURDATE()");
$stats['today_feedback'] = $stmt->fetch()['today_feedback'];

// Feedback this month
$stmt = $pdo->query("SELECT COUNT(*) as month_feedback FROM feedback WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$stats['month_feedback'] = $stmt->fetch()['month_feedback'];

// Average rating
$stmt = $pdo->query("SELECT AVG(rating) as avg_rating FROM feedback");
$avg_rating_result = $stmt->fetch();
$stats['avg_rating'] = $avg_rating_result['avg_rating'] ? round($avg_rating_result['avg_rating'], 1) : 0;

// Get all feedback with user and course information
$stmt = $pdo->query("
    SELECT 
        f.feedback_id,
        f.rating,
        f.comment,
        f.created_at,
        u.name as user_name,
        u.email as user_email,
        c.title as course_name
    FROM feedback f
    JOIN users u ON f.user_id = u.user_id
    JOIN courses c ON f.course_id = c.course_id
    ORDER BY f.created_at DESC
");
$feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Management - CodeLearn Admin</title>
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
            padding: 30px;
        }

        .header {
            background: white;
            padding: 28px 36px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border: 1px solid #E5E7EB;
        }

        .header-title {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #1E293B 0%, #3B82F6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }

        .header-subtitle {
            color: #6B7280;
            font-size: 15px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 16px;
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

        .stat-card.blue { --card-color: #3B82F6; --card-color-light: #60A5FA; }
        .stat-card.purple { --card-color: #8B5CF6; --card-color-light: #A78BFA; }
        .stat-card.green { --card-color: #10B981; --card-color-light: #34D399; }
        .stat-card.orange { --card-color: #F59E0B; --card-color-light: #FBBF24; }

        .stat-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        /* Feedback Grid */
        .feedback-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .feedback-card {
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .feedback-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            border-color: #6366f1;
        }

        .feedback-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        /* Delete Confirmation Modal */
.delete-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(8px);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.delete-modal.active {
    display: flex;
    opacity: 1;
}

.delete-modal-content {
    background: white;
    border-radius: 20px;
    padding: 32px;
    max-width: 450px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    transform: scale(0.9) translateY(20px);
    transition: transform 0.3s ease;
}

.delete-modal.active .delete-modal-content {
    transform: scale(1) translateY(0);
}

.delete-modal-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
}

.delete-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, #EF4444, #DC2626);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
    box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);
}

.delete-modal-title {
    flex: 1;
}

.delete-modal-title h3 {
    font-size: 22px;
    color: #1f2937;
    margin-bottom: 4px;
    font-weight: 700;
}

.delete-modal-title p {
    font-size: 14px;
    color: #6b7280;
}

.delete-modal-body {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 24px;
}

.delete-modal-body p {
    color: #991b1b;
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
}

.delete-modal-footer {
    display: flex;
    gap: 12px;
}

.modal-btn {
    flex: 1;
    padding: 14px 20px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.modal-btn-cancel {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #e5e7eb;
}

.modal-btn-cancel:hover {
    background: #e5e7eb;
}

.modal-btn-delete {
    background: linear-gradient(135deg, #EF4444, #DC2626);
    color: white;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.modal-btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
}

.modal-btn-delete:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

        .user-avatar {
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

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 600;
            font-size: 16px;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .user-email {
            font-size: 12px;
            color: #9ca3af;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
            margin-bottom: 12px;
        }

        .course-badge i {
            font-size: 10px;
        }

        .rating-stars {
            color: #fbbf24;
            font-size: 20px;
            letter-spacing: 2px;
            margin-bottom: 12px;
        }

        .feedback-text {
            color: #4b5563;
            font-size: 14px;
            line-height: 1.6;
            font-style: italic;
            margin-bottom: 16px;
        }

        .feedback-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid #f3f4f6;
        }

        .feedback-date {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #9ca3af;
        }

        .feedback-date i {
            font-size: 11px;
        }

        .delete-btn {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }

        .delete-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        .no-feedback {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 20px;
            background: white;
            border: 2px dashed #e5e7eb;
            border-radius: 16px;
        }

        .no-feedback-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .no-feedback-title {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .no-feedback-text {
            color: #6b7280;
            font-size: 15px;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @media (max-width: 1024px) {
            .feedback-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .feedback-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1 class="header-title">Feedback Management</h1>
        <p class="header-subtitle">View and manage user feedback from all courses</p>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-content">
                <div class="stat-info">
                    <h2><?php echo number_format($stats['total_feedback']); ?></h2>
                    <p>Total Feedback</p>
                </div>
                <div class="stat-icon"><i class="fas fa-comments"></i></div>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-content">
                <div class="stat-info">
                    <h2><?php echo $stats['today_feedback']; ?></h2>
                    <p>Today's Feedback</p>
                </div>
                <div class="stat-icon"><i class="fas fa-comment-dots"></i></div>
            </div>
        </div>

        <div class="stat-card purple">
            <div class="stat-content">
                <div class="stat-info">
                    <h2><?php echo $stats['month_feedback']; ?></h2>
                    <p>This Month</p>
                </div>
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            </div>
        </div>

        <div class="stat-card orange">
            <div class="stat-content">
                <div class="stat-info">
                    <h2><?php echo $stats['avg_rating']; ?> ★</h2>
                    <p>Average Rating</p>
                </div>
                <div class="stat-icon"><i class="fas fa-star"></i></div>
            </div>
        </div>
    </div>

    <!-- Feedback Grid -->
    <div class="feedback-grid">
        <?php if (!empty($feedbacks)): ?>
            <?php foreach($feedbacks as $feedback): ?>
                <div class="feedback-card">
                    <div class="feedback-header">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($feedback['user_name'], 0, 1)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($feedback['user_name']); ?></div>
                            <div class="user-email"><?php echo htmlspecialchars($feedback['user_email']); ?></div>
                        </div>
                    </div>
                    
                    <div class="course-badge">
                        <i class="fas fa-book"></i>
                        <?php echo htmlspecialchars($feedback['course_name']); ?>
                    </div>
                    
                    <div class="rating-stars">
                        <?php 
                        for($i = 1; $i <= 5; $i++) {
                            echo $i <= $feedback['rating'] ? '★' : '☆';
                        }
                        ?>
                    </div>
                    
                    <div class="feedback-text">
                        "<?php echo htmlspecialchars($feedback['comment']); ?>"
                    </div>
                    
                    <div class="feedback-footer">
                        <div class="feedback-date">
                            <i class="fas fa-clock"></i>
                            <?php echo date('M j, Y', strtotime($feedback['created_at'])); ?>
                        </div>
                        
                        <button type="button" class="delete-btn" onclick="showDeleteModal(<?php echo $feedback['feedback_id']; ?>, '<?php echo htmlspecialchars($feedback['user_name'], ENT_QUOTES); ?>')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-feedback">
                <div class="no-feedback-icon">💬</div>
                <div class="no-feedback-title">No Feedback Yet</div>
                <div class="no-feedback-text">User feedback will appear here once submitted</div>
            </div>
        <?php endif; ?>
    </div>
    <!-- Delete Confirmation Modal -->
<div class="delete-modal" id="deleteModal">
    <div class="delete-modal-content">
        <div class="delete-modal-header">
            <div class="delete-icon-wrapper">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="delete-modal-title">
                <h3>Delete Feedback</h3>
                <p>This action cannot be undone</p>
            </div>
        </div>
        
        <div class="delete-modal-body">
            <p>
                Are you sure you want to delete feedback from <strong id="deleteUserName"></strong>? 
                This will permanently remove this feedback from your system.
            </p>
        </div>
        
        <div class="delete-modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-delete" id="confirmDeleteBtn" onclick="confirmDelete()">
                <i class="fas fa-trash"></i> Delete Feedback
            </button>
        </div>
    </div>
</div>
    <script>
let currentDeleteId = null;

function showDeleteModal(feedbackId, userName) {
    currentDeleteId = feedbackId;
    const modal = document.getElementById('deleteModal');
    document.getElementById('deleteUserName').textContent = userName;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
    currentDeleteId = null;
}

function confirmDelete() {
    if (!currentDeleteId) return;
    
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    deleteBtn.disabled = true;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="feedback_id" value="${currentDeleteId}">
        <input type="hidden" name="delete_feedback" value="1">
    `;
    document.body.appendChild(form);
    form.submit();
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
</body>
</html>