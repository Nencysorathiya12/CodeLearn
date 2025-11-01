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

// Get course_id from URL
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Get course details
$stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Course not found!");
}

// Get all lessons for this course
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY created_at ASC");
$stmt->execute([$course_id]);
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Course icons mapping with specific colors
$course_icons = [
    'python' => ['icon' => '🐍', 'color' => '#3776AB'],
    'java' => ['icon' => '☕', 'color' => '#007396'],
    'javascript' => ['icon' => '⚡', 'color' => '#F7DF1E'],
    'js' => ['icon' => '⚡', 'color' => '#F7DF1E'],
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
    'c' => ['icon' => '©️', 'color' => '#A8B9CC'],
    'angular' => ['icon' => '🅰️', 'color' => '#DD0031'],
    'mongo' => ['icon' => '🍃', 'color' => '#47A248'],
    'mongodb' => ['icon' => '🍃', 'color' => '#47A248'],
    'ai' => ['icon' => '🤖', 'color' => '#FF6F00'],
    'artificial intelligence' => ['icon' => '🤖', 'color' => '#FF6F00'],
    'vue' => ['icon' => '💚', 'color' => '#4FC08D'],
    'cyber' => ['icon' => '🔒', 'color' => '#000000'],
    'security' => ['icon' => '🛡️', 'color' => '#000000'],
    'cybersecurity' => ['icon' => '🛡️', 'color' => '#000000'],
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

$course_icon = getCourseIcon($course['title']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course['title']); ?> - Learning Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #ffffff;
            color: #333;
            line-height: 1.6;
            overflow: hidden;
        }

        .container {
            display: flex;
            height: 100vh;
            width: 100vw;
            background: #ffffff;
        }

        /* Sidebar - Dynamic color based on course */
        .sidebar {
            width: 320px;
            background: linear-gradient(135deg, <?php echo htmlspecialchars($course_icon['color']); ?>15, <?php echo htmlspecialchars($course_icon['color']); ?>08);
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.1);
            border-right: 3px solid <?php echo htmlspecialchars($course_icon['color']); ?>;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 2px solid <?php echo htmlspecialchars($course_icon['color']); ?>30;
            flex-shrink: 0;
        }

        .course-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .course-icon-large {
            font-size: 28px;
            padding: 8px;
            border-radius: 8px;
            background: <?php echo htmlspecialchars($course_icon['color']); ?>20;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 44px;
            height: 44px;
        }

        .platform-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: <?php echo htmlspecialchars($course_icon['color']); ?>;
            line-height: 1.2;
            flex: 1;
        }

        .search-box {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid <?php echo htmlspecialchars($course_icon['color']); ?>40;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            transition: all 0.3s ease;
        }

        .search-box:focus {
            outline: none;
            border-color: <?php echo htmlspecialchars($course_icon['color']); ?>;
            box-shadow: 0 0 8px <?php echo htmlspecialchars($course_icon['color']); ?>30;
        }

        .chapters-section {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .chapters-section h3 {
            font-size: 16px;
            color: <?php echo htmlspecialchars($course_icon['color']); ?>;
            margin-bottom: 15px;
            font-weight: 600;
            text-align: center;
            background: rgba(255, 255, 255, 0.8);
            padding: 10px;
            border-radius: 8px;
        }

        .chapter-item-wrapper {
            position: relative;
            margin-bottom: 6px;
        }

        .chapter-item {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            padding-right: 50px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            background: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            font-size: 14px;
        }

        .chapter-text {
            flex: 1;
        }

        .edit-lesson-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #667eea;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .edit-lesson-btn:hover {
            background: #5568d3;
            transform: translateY(-50%) scale(1.1);
        }

        .edit-lesson-btn i {
            font-size: 12px;
        }

        .chapter-item:hover {
            background: white;
            transform: translateX(5px);
            box-shadow: 0 3px 8px <?php echo htmlspecialchars($course_icon['color']); ?>25;
            border-color: <?php echo htmlspecialchars($course_icon['color']); ?>50;
        }

        .chapter-item.active {
            background: <?php echo htmlspecialchars($course_icon['color']); ?>;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px <?php echo htmlspecialchars($course_icon['color']); ?>40;
        }

        .chapter-item.completed {
            background: <?php echo htmlspecialchars($course_icon['color']); ?>20;
            border-color: <?php echo htmlspecialchars($course_icon['color']); ?>;
            color: <?php echo htmlspecialchars($course_icon['color']); ?>;
        }

        .chapter-icon {
            margin-right: 12px;
            font-size: 14px;
            min-width: 18px;
            text-align: center;
        }

        .chapter-icon.play {
            color: white;
        }

        .chapter-icon.check {
            color: <?php echo htmlspecialchars($course_icon['color']); ?>;
            font-weight: bold;
        }

        .chapter-icon.circle {
            color: #6c757d;
            border: 2px solid currentColor;
            border-radius: 50%;
            width: 14px;
            height: 14px;
            display: inline-block;
        }

        .action-buttons {
            padding: 20px;
            border-top: 2px solid <?php echo htmlspecialchars($course_icon['color']); ?>30;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.5);
        }

        .btn {
            width: 100%;
            padding: 12px 18px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            text-decoration: none;
        }

        .btn-back {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(108,117,125,0.3);
        }

        .btn-quiz {
            background: linear-gradient(135deg, #6f42c1, #5a2d91);
            color: white;
        }

        .btn-quiz:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(111,66,193,0.3);
        }

        .btn-icon {
            margin-right: 8px;
            font-size: 14px;
        }

        /* Main Content */
        .main-content {
            margin-left: 320px;
            width: calc(100vw - 320px);
            height: 100vh;
            overflow-y: auto;
            background: #fff;
            position: relative;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #28a745;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .welcome-message {
            padding: 60px 40px;
            text-align: center;
            color: #666;
            background: #ffffff;
        }

        .welcome-message h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 20px;
        }

        .welcome-message p {
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 30px;
            line-height: 1.6;
        }

        .welcome-card {
            background: <?php echo htmlspecialchars($course_icon['color']); ?>15;
            border: 2px solid <?php echo htmlspecialchars($course_icon['color']); ?>;
            border-radius: 16px;
            padding: 30px;
            max-width: 500px;
            margin: 0 auto;
        }

        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            max-width: 600px;
            margin: 40px auto;
        }

        /* Content Wrapper */
        .lesson-content-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            background: #ffffff;
        }

        .lesson-inner-content {
            width: 100%;
            max-width: 1000px;
            padding: 40px 60px;
        }

        /* Mobile */
        .mobile-header {
            display: none;
            background: white;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1001;
        }

        .mobile-menu-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #333;
        }

        .mobile-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .mobile-header {
                display: flex;
            }

            .sidebar {
                transform: translateX(-100%);
                width: 300px;
                top: 60px;
                height: calc(100vh - 60px);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100vw;
                padding-top: 60px;
                height: calc(100vh - 60px);
            }

            .lesson-inner-content {
                padding: 20px 25px;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .empty-lessons {
            text-align: center;
            padding: 80px 40px;
            color: #666;
        }

        .empty-lessons-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="mobile-header">
        <button class="mobile-menu-btn" onclick="toggleSidebar()">☰</button>
        <div class="mobile-title"><?php echo htmlspecialchars($course['title']); ?></div>
        <div></div>
    </div>

    <div class="container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="course-info">
                    <div class="course-icon-large" style="color: <?php echo is_array($course_icon) ? $course_icon['color'] : '#6B7280'; ?>;">
                        <?php echo is_array($course_icon) ? $course_icon['icon'] : $course_icon; ?>
                    </div>
                    <div class="platform-title"><?php echo htmlspecialchars($course['title']); ?></div>
                </div>
                <input type="text" class="search-box" placeholder="Search lessons..." onkeyup="filterLessons(this.value)">
            </div>

            <div class="chapters-section">
                <h3>Lessons (<?php echo count($lessons); ?>)</h3>
                
                <?php if (count($lessons) > 0): ?>
                    <?php foreach ($lessons as $index => $lesson): ?>
                    <div class="chapter-item-wrapper">
                        <div class="chapter-item" onclick="loadLesson('<?php echo $lesson['lesson_file']; ?>', '<?php echo htmlspecialchars($lesson['lesson_title'], ENT_QUOTES); ?>', this)" data-title="<?php echo htmlspecialchars($lesson['lesson_title']); ?>">
                            <span class="chapter-icon circle">○</span>
                            <span class="chapter-text"><?php echo htmlspecialchars($lesson['lesson_title']); ?></span>
                        </div>
                        <?php if (isset($_SESSION['admin_id'])): ?>
                        <a href="edit_lesson.php?lesson_id=<?php echo $lesson['lesson_id']; ?>" class="edit-lesson-btn" title="Edit Lesson" onclick="event.stopPropagation()">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-lessons">
                        <div class="empty-lessons-icon">📚</div>
                        <p style="color: #6c757d;">No lessons available yet</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="action-buttons">
                <a href="courses.php" class="btn btn-back">
                    <span class="btn-icon">←</span>
                    Back to Courses
                </a>
                <button class="btn btn-quiz" onclick="takeQuiz()">
                    <span class="btn-icon">🎯</span>
                    Take Quiz
                </button>
            </div>
        </div>

        <div class="main-content" id="main-content">
            <div class="loading-overlay" id="loading-overlay">
                <div class="loading-spinner"></div>
            </div>

            <div class="welcome-message fade-in">
                <h1>Welcome to <?php echo htmlspecialchars($course['title']); ?></h1>
                <p><?php echo htmlspecialchars($course['description']); ?></p>
                <div class="welcome-card">
                    <h3 style="color: <?php echo htmlspecialchars($course_icon['color']); ?>; margin-bottom: 15px;">Getting Started</h3>
                    <p style="margin-bottom: 0; font-size: 14px;">
                        <?php if (count($lessons) > 0): ?>
                            Click on any lesson from the sidebar to begin your learning journey.
                        <?php else: ?>
                            Lessons for this course are coming soon!
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentLesson = null;
        let completedLessons = [];

        function loadLesson(fileName, title, element) {
            currentLesson = fileName;
            updateActiveLesson(element);
            
            const loadingOverlay = document.getElementById('loading-overlay');
            loadingOverlay.classList.add('show');

            const filePath = 'uploads/lessons/' + fileName;
            
            fetch(filePath)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`File not found: ${response.status}`);
                    }
                    return response.text();
                })
                .then(html => {
                    displayLessonContent(title, html);
                    loadingOverlay.classList.remove('show');
                    markLessonComplete(fileName);
                    closeMobileSidebar();
                })
                .catch(error => {
                    console.error('Error loading lesson:', error);
                    showErrorMessage(title, error.message);
                    loadingOverlay.classList.remove('show');
                    closeMobileSidebar();
                });
        }

        function updateActiveLesson(element) {
            document.querySelectorAll('.chapter-item-wrapper .chapter-item').forEach(item => {
                item.classList.remove('active');
                const icon = item.querySelector('.chapter-icon');
                if (!item.classList.contains('completed')) {
                    icon.innerHTML = '○';
                    icon.className = 'chapter-icon circle';
                }
            });
            
            element.classList.add('active');
            const icon = element.querySelector('.chapter-icon');
            icon.innerHTML = '▶';
            icon.className = 'chapter-icon play';
        }

        function displayLessonContent(title, htmlContent) {
            document.title = title + ' - <?php echo htmlspecialchars($course['title']); ?>';
            
            const mainContent = document.getElementById('main-content');
            const parser = new DOMParser();
            const doc = parser.parseFromString(htmlContent, 'text/html');
            
            // Extract all styles from the lesson
            let lessonStyles = '';
            const styleElements = doc.querySelectorAll('style, link[rel="stylesheet"]');
            styleElements.forEach(el => {
                if (el.tagName === 'STYLE') {
                    lessonStyles += `<style>${el.innerHTML}</style>`;
                } else {
                    lessonStyles += el.outerHTML;
                }
            });
            
            // Extract body content (this handles lessons with full HTML structure)
            let bodyContent = '';
            if (doc.body) {
                bodyContent = doc.body.innerHTML;
            } else {
                bodyContent = htmlContent;
            }
            
            // Remove any padding/margin from body if it exists in the lesson styles
            const overrideStyles = `
                <style>
                    .lesson-inner-content * {
                        max-width: 100%;
                    }
                    /* Override any body styles from lesson */
                    .lesson-inner-content {
                        padding: 40px 60px !important;
                        margin: 0 auto !important;
                    }
                    @media (max-width: 768px) {
                        .lesson-inner-content {
                            padding: 20px 25px !important;
                        }
                    }
                </style>
            `;
            
            // Inject content with proper centering
            mainContent.innerHTML = `
                <div class="loading-overlay" id="loading-overlay">
                    <div class="loading-spinner"></div>
                </div>
                ${lessonStyles}
                ${overrideStyles}
                <div class="lesson-content-wrapper fade-in">
                    <div class="lesson-inner-content">
                        ${bodyContent}
                    </div>
                </div>
            `;
            
            // Handle scripts from the lesson
            const scripts = doc.querySelectorAll('script');
            scripts.forEach(script => {
                const newScript = document.createElement('script');
                if (script.src) {
                    newScript.src = script.src;
                    document.head.appendChild(newScript);
                } else if (script.textContent) {
                    newScript.textContent = script.textContent;
                    document.body.appendChild(newScript);
                }
            });

            // Scroll to top
            mainContent.scrollTo(0, 0);
        }

        function showErrorMessage(title, error) {
            const mainContent = document.getElementById('main-content');
            mainContent.innerHTML = `
                <div class="loading-overlay" id="loading-overlay">
                    <div class="loading-spinner"></div>
                </div>
                <div class="lesson-content-wrapper fade-in">
                    <div class="lesson-inner-content">
                        <div class="error-message">
                            <h2>Content Not Available</h2>
                            <p>Sorry, "${title}" could not be loaded.</p>
                            <p><small>Error: ${error}</small></p>
                        </div>
                    </div>
                </div>
            `;
        }

        function markLessonComplete(fileName) {
            if (!completedLessons.includes(fileName)) {
                completedLessons.push(fileName);
            }
        }

        function filterLessons(searchTerm) {
            const items = document.querySelectorAll('.chapter-item');
            const searchLower = searchTerm.toLowerCase();
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchLower) ? 'flex' : 'none';
            });
        }

        function takeQuiz() {
            window.location.href = 'quiz.php?course_id=<?php echo $course_id; ?>';
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }

        function closeMobileSidebar() {
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.remove('open');
            }
        }

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
                
                if (!sidebar.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                document.getElementById('sidebar').classList.remove('open');
            }
        });
    </script>
</body>
</html>