<?php
// Run this file once to check and fix your database
// http://localhost/Learning_platform/check_and_fix.php

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

echo "<style>
body { font-family: Arial, sans-serif; padding: 30px; background: #f5f5f5; }
.success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0; border: 1px solid #c3e6cb; }
.error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 10px 0; border: 1px solid #f5c6cb; }
.info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; margin: 10px 0; border: 1px solid #bee5eb; }
.warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin: 10px 0; border: 1px solid #ffeaa7; }
table { width: 100%; background: white; border-collapse: collapse; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
th { background: #667eea; color: white; }
h2 { color: #2d3748; margin-top: 30px; }
button { background: #667eea; color: white; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 16px; margin: 10px 5px; }
button:hover { background: #5568d3; }
code { background: #2d3748; color: #48bb78; padding: 2px 6px; border-radius: 4px; }
</style>";

echo "<h1>🔍 Database Structure Check & Auto Fix</h1>";

// Step 1: Check courses table structure
echo "<h2>Step 1: Checking COURSES Table</h2>";
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM courses");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table><tr><th>Column Name</th><th>Type</th><th>Status</th></tr>";
    
    $hasCourseName = false;
    $hasCourseId = false;
    $courseNameColumn = null;
    
    foreach ($columns as $col) {
        $status = '✅';
        if ($col['Field'] === 'course_name') {
            $hasCourseName = true;
            $courseNameColumn = 'course_name';
            $status = '✅ Found (Perfect!)';
        } elseif (in_array($col['Field'], ['title', 'name'])) {
            $courseNameColumn = $col['Field'];
            $status = '⚠️ Found as "' . $col['Field'] . '" (Needs rename)';
        }
        if ($col['Field'] === 'course_id') {
            $hasCourseId = true;
        }
        echo "<tr><td><strong>{$col['Field']}</strong></td><td>{$col['Type']}</td><td>$status</td></tr>";
    }
    echo "</table>";
    
    // Auto-fix courses table
    if (!$hasCourseName && $courseNameColumn) {
        echo "<div class='warning'>⚠️ Column found as '$courseNameColumn', renaming to 'course_name'...</div>";
        try {
            $pdo->exec("ALTER TABLE courses CHANGE `$courseNameColumn` `course_name` VARCHAR(200)");
            echo "<div class='success'>✅ Successfully renamed '$courseNameColumn' to 'course_name'</div>";
            $hasCourseName = true;
        } catch (Exception $e) {
            echo "<div class='error'>❌ Failed to rename: " . $e->getMessage() . "</div>";
        }
    } elseif (!$hasCourseName) {
        echo "<div class='warning'>⚠️ No course name column found. Adding 'course_name' column...</div>";
        try {
            $pdo->exec("ALTER TABLE courses ADD COLUMN `course_name` VARCHAR(200) NOT NULL DEFAULT 'Untitled Course' AFTER `course_id`");
            echo "<div class='success'>✅ Successfully added 'course_name' column</div>";
            $hasCourseName = true;
        } catch (Exception $e) {
            echo "<div class='error'>❌ Failed to add column: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='success'>✅ 'course_name' column exists - Perfect!</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error checking courses table: " . $e->getMessage() . "</div>";
}

// Step 2: Check users table for plan column
echo "<h2>Step 2: Checking USERS Table</h2>";
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table><tr><th>Column Name</th><th>Type</th><th>Status</th></tr>";
    
    $hasPlan = false;
    foreach ($columns as $col) {
        $status = '✅';
        if ($col['Field'] === 'plan') {
            $hasPlan = true;
            $status = '✅ Found (Perfect!)';
        }
        echo "<tr><td><strong>{$col['Field']}</strong></td><td>{$col['Type']}</td><td>$status</td></tr>";
    }
    echo "</table>";
    
    if (!$hasPlan) {
        echo "<div class='warning'>⚠️ 'plan' column not found. Adding...</div>";
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN `plan` ENUM('free','pro','max') DEFAULT 'free' AFTER `password`");
            echo "<div class='success'>✅ Successfully added 'plan' column</div>";
            $pdo->exec("UPDATE users SET plan = 'free' WHERE plan IS NULL");
            echo "<div class='success'>✅ Updated all users to 'free' plan</div>";
        } catch (Exception $e) {
            echo "<div class='error'>❌ Failed to add plan column: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='success'>✅ 'plan' column exists - Perfect!</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error checking users table: " . $e->getMessage() . "</div>";
}

// Step 3: Check/Create certificates table
echo "<h2>Step 3: Checking CERTIFICATES Table</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'certificates'");
    if ($stmt->rowCount() > 0) {
        echo "<div class='success'>✅ Certificates table exists</div>";
        
        $stmt = $pdo->query("SHOW COLUMNS FROM certificates");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<table><tr><th>Column Name</th><th>Type</th></tr>";
        foreach ($columns as $col) {
            echo "<tr><td><strong>{$col['Field']}</strong></td><td>{$col['Type']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='warning'>⚠️ Certificates table doesn't exist. Creating...</div>";
        try {
            $pdo->exec("
                CREATE TABLE `certificates` (
                    `certificate_id` INT AUTO_INCREMENT PRIMARY KEY,
                    `user_id` INT NOT NULL,
                    `course_id` INT NOT NULL,
                    `certificate_type` ENUM('course_completion', 'achievement') NOT NULL DEFAULT 'course_completion',
                    `certificate_file` VARCHAR(255) NOT NULL,
                    `certificate_number` VARCHAR(50) UNIQUE NOT NULL,
                    `issued_date` DATE NOT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX `idx_user` (`user_id`),
                    INDEX `idx_course` (`course_id`),
                    INDEX `idx_cert_number` (`certificate_number`),
                    UNIQUE KEY `unique_user_course` (`user_id`, `course_id`),
                    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
                    FOREIGN KEY (`course_id`) REFERENCES `courses`(`course_id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            echo "<div class='success'>✅ Successfully created certificates table</div>";
        } catch (Exception $e) {
            echo "<div class='error'>❌ Failed to create table: " . $e->getMessage() . "</div>";
        }
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
}

// Step 4: Check courses data
echo "<h2>Step 4: Checking Courses Data</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM courses");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] == 0) {
        echo "<div class='warning'>⚠️ No courses found. Adding sample courses...</div>";
        $pdo->exec("
            INSERT INTO courses (course_name) VALUES 
            ('Complete Web Development Bootcamp'),
            ('Python Programming Masterclass'),
            ('React.js Complete Course'),
            ('Data Science with Python'),
            ('JavaScript Algorithms and Data Structures'),
            ('Node.js Backend Development'),
            ('MySQL Database Design'),
            ('PHP Full Stack Development'),
            ('Mobile App Development with Flutter'),
            ('UI/UX Design Fundamentals')
        ");
        echo "<div class='success'>✅ Added 10 sample courses</div>";
    } else {
        echo "<div class='success'>✅ Found {$result['count']} courses</div>";
        
        // Show sample courses
        $stmt = $pdo->query("SELECT course_id, course_name FROM courses LIMIT 5");
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table><tr><th>ID</th><th>Course Name</th></tr>";
        foreach ($courses as $course) {
            echo "<tr><td>{$course['course_id']}</td><td>{$course['course_name']}</td></tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
}

// Step 5: Create test PRO user
echo "<h2>Step 5: Test User Setup</h2>";
try {
    // Check if test user exists
    $stmt = $pdo->prepare("SELECT user_id, plan FROM users WHERE email = ?");
    $stmt->execute(['testpro@codelearn.com']);
    $testUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$testUser) {
        // Create test user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, plan) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            'Test Pro User',
            'testpro@codelearn.com',
            password_hash('password', PASSWORD_DEFAULT),
            'pro'
        ]);
        echo "<div class='success'>✅ Created test PRO user<br>
        📧 Email: testpro@codelearn.com<br>
        🔑 Password: password</div>";
    } else {
        // Update to PRO if not already
        if ($testUser['plan'] !== 'pro') {
            $pdo->exec("UPDATE users SET plan = 'pro' WHERE email = 'testpro@codelearn.com'");
            echo "<div class='success'>✅ Updated test user to PRO plan</div>";
        }
        echo "<div class='info'>ℹ️ Test user already exists<br>
        📧 Email: testpro@codelearn.com<br>
        🔑 Password: password</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
}

// Step 6: Create certificates folder
echo "<h2>Step 6: Certificates Folder</h2>";
$certDir = __DIR__ . '/certificates';
if (!file_exists($certDir)) {
    if (mkdir($certDir, 0755, true)) {
        echo "<div class='success'>✅ Created 'certificates' folder</div>";
    } else {
        echo "<div class='error'>❌ Failed to create 'certificates' folder. Please create it manually.</div>";
    }
} else {
    echo "<div class='success'>✅ 'certificates' folder already exists</div>";
}

// Final Summary
echo "<h2>🎉 Setup Summary</h2>";
echo "<div class='info'>
<strong>All checks complete! Here's what you can do now:</strong><br><br>
1. ✅ Database is ready<br>
2. ✅ Courses table has 'course_name' column<br>
3. ✅ Users table has 'plan' column<br>
4. ✅ Certificates table exists<br>
5. ✅ Sample courses added<br>
6. ✅ Test PRO user created<br>
7. ✅ Certificates folder ready<br><br>

<strong>🧪 Test Now:</strong><br>
<a href='certificates.php' target='_blank' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block; margin-top: 10px;'>
Go to Certificates Page →
</a>
<br><br>

<strong>📝 Login with:</strong><br>
Email: testpro@codelearn.com<br>
Password: password<br>
Plan: PRO (Can generate certificates)
</div>";

echo "<div class='success' style='margin-top: 20px; font-size: 18px; text-align: center;'>
<strong>🎓 Your Certificate System is Ready!</strong>
</div>";
?>