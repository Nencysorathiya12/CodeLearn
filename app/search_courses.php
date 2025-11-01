<?php
session_start();
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'codelearn_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database connection failed'
    ]);
    exit;
}

// Get search query
$searchQuery = isset($_POST['query']) ? trim($_POST['query']) : '';

if (empty($searchQuery)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a search term']);
    exit;
}

// Course keyword mappings with aliases - EXACT match with your database course titles
$courseKeywords = [
    'python' => ['python', 'py', 'python programming', 'python course', 'learn python', 'python basics'],
    'python ai' => ['python ai', 'python for ai', 'ai python', 'machine learning python', 'python ml', 'ai with python'],
    'java' => ['java', 'java programming', 'core java', 'learn java', 'java basics'],
    'javascript' => ['javascript', 'js', 'java script', 'ecmascript', 'learn javascript', 'learn js'],
    'node' => ['node', 'nodejs', 'node.js', 'node js', 'backend node', 'node backend'],
    'react' => ['react', 'reactjs', 'react.js', 'react js', 'learn react', 'react frontend'],
    'html' => ['html', 'html5', 'hypertext', 'learn html', 'html basics'],
    'css' => ['css', 'css3', 'cascading', 'learn css', 'styling', 'css basics'],
    'express' => ['express', 'expressjs', 'express.js', 'express framework', 'express backend'],
    'sql' => ['sql', 'mysql', 'structured query language', 'database sql', 'learn sql'],
    'php' => ['php', 'hypertext preprocessor', 'learn php', 'php programming', 'php basics'],
    'dsa' => ['dsa', 'data structures', 'algorithms', 'data structure', 'ds algo', 'ds and algo'],
    'c++' => ['c++', 'cpp', 'c plus plus', 'cplusplus', 'learn cpp', 'c++ programming'],
    'angular' => ['angular', 'angularjs', 'angular framework', 'learn angular'],
    'mongo' => ['mongo', 'mongodb', 'mongoose', 'nosql', 'mongodb database'],
    'ai' => ['ai', 'artificial intelligence', 'machine learning', 'ml', 'deep learning', 'learn ai'],
    'vue' => ['vue', 'vuejs', 'vue.js', 'vue framework', 'learn vue'],
    'cyber' => ['cyber', 'cybersecurity', 'cyber security', 'security', 'ethical hacking', 'learn cybersecurity'],
];

// Normalize search query
$normalizedQuery = strtolower($searchQuery);
$normalizedQuery = preg_replace('/[^a-z0-9\s+]/', '', $normalizedQuery);
$normalizedQuery = trim($normalizedQuery);

// Find matching course keyword
$matchedCourse = null;
$matchedKeyword = null;

// First try exact match
foreach ($courseKeywords as $course => $keywords) {
    foreach ($keywords as $keyword) {
        // Exact match
        if ($normalizedQuery === $keyword) {
            $matchedCourse = $course;
            $matchedKeyword = $keyword;
            break 2;
        }
        // Contains match
        if (strpos($normalizedQuery, $keyword) !== false || 
            strpos($keyword, $normalizedQuery) !== false) {
            if ($matchedCourse === null) {
                $matchedCourse = $course;
                $matchedKeyword = $keyword;
            }
        }
    }
}

// If no exact match, try fuzzy matching
if ($matchedCourse === null) {
    $suggestions = getSuggestions($normalizedQuery, $courseKeywords);
    
    echo json_encode([
        'success' => false, 
        'message' => 'No course found for "' . htmlspecialchars($searchQuery) . '"',
        'suggestions' => $suggestions
    ]);
    exit;
}

// Search in database for the course
try {
    // Build search pattern
    $searchPattern = '%' . str_replace(' ', '%', $matchedCourse) . '%';
    
    // Search in courses table
    $stmt = $pdo->prepare("
        SELECT c.course_id, c.title, c.description, c.type,
               COUNT(l.lesson_id) as lesson_count
        FROM courses c
        LEFT JOIN lessons l ON c.course_id = l.course_id
        WHERE c.status = 'published' 
        AND (LOWER(c.title) LIKE ? OR LOWER(c.description) LIKE ?)
        GROUP BY c.course_id
        ORDER BY lesson_count DESC, c.created_at DESC
        LIMIT 1
    ");
    
    $stmt->execute([$searchPattern, $searchPattern]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($course) {
        // Check if course has lessons
        $lessonCheck = $pdo->prepare("SELECT COUNT(*) as count FROM lessons WHERE course_id = ?");
        $lessonCheck->execute([$course['course_id']]);
        $lessonCount = $lessonCheck->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Success response with course details
        $response = [
            'success' => true,
            'course_id' => $course['course_id'],
            'course_title' => $course['title'],
            'course_type' => $course['type'],
            'lesson_count' => $lessonCount,
            'matched_keyword' => $matchedCourse
        ];
        
        // Redirect to lessons page if lessons exist, otherwise to course detail page
        if ($lessonCount > 0) {
            $response['redirect_url'] = 'lessons.php?course_id=' . $course['course_id'];
            $response['message'] = 'Found ' . $lessonCount . ' lessons for ' . $course['title'];
        } else {
            $response['redirect_url'] = 'courses.php?id=' . $course['course_id'];
            $response['message'] = 'Course found but no lessons available yet';
        }
        
        echo json_encode($response);
    } else {
        // Course not found in database
        echo json_encode([
            'success' => false,
            'message' => 'Course "' . htmlspecialchars($matchedCourse) . '" not available yet',
            'matched_keyword' => $matchedCourse,
            'suggestions' => getAvailableCourses($pdo)
        ]);
    }
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred',
        'error' => $e->getMessage()
    ]);
}

// Function to get search suggestions based on similarity
function getSuggestions($query, $keywords) {
    $suggestions = [];
    $maxSuggestions = 3;
    $scored = [];
    
    foreach ($keywords as $course => $courseKeywords) {
        $maxScore = 0;
        foreach ($courseKeywords as $keyword) {
            $similarity = 0;
            similar_text($query, $keyword, $similarity);
            
            // Also check Levenshtein distance for better matching
            $distance = levenshtein(substr($query, 0, 255), substr($keyword, 0, 255));
            $levScore = (1 - $distance / max(strlen($query), strlen($keyword))) * 100;
            
            $score = max($similarity, $levScore);
            
            if ($score > $maxScore) {
                $maxScore = $score;
            }
        }
        
        if ($maxScore > 40) {
            $scored[$course] = $maxScore;
        }
    }
    
    arsort($scored);
    $suggestions = array_slice(array_keys($scored), 0, $maxSuggestions);
    
    // Capitalize first letter
    $suggestions = array_map('ucfirst', $suggestions);
    
    return $suggestions;
}

// Function to get available courses from database
function getAvailableCourses($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT title 
            FROM courses 
            WHERE status = 'published' 
            ORDER BY created_at DESC 
            LIMIT 3
        ");
        $stmt->execute();
        $courses = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $courses ?: [];
    } catch(PDOException $e) {
        return [];
    }
}
?>