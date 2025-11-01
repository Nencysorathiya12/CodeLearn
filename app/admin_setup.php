<?php
// admin_setup.php - Run this file once to setup/fix admin account

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codelearn_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if admin table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'admin'");
    if ($tableCheck->rowCount() == 0) {
        // Create admin table
        $createTable = "CREATE TABLE `admin` (
            `admin_id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `email` varchar(100) NOT NULL UNIQUE,
            `password` varchar(255) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`admin_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $pdo->exec($createTable);
        echo "Admin table created successfully.<br>";
    }
    
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->execute(['admin@codelearn.com']);
    $admin = $stmt->fetch();
    
    if ($admin) {
        // Update existing admin with new password
        $hashedPassword = password_hash('admin@123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin SET password = ?, name = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, 'Admin User', 'admin@codelearn.com']);
        echo "Admin password updated successfully.<br>";
    } else {
        // Create new admin
        $hashedPassword = password_hash('admin@123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute(['Admin User', 'admin@codelearn.com', $hashedPassword]);
        echo "New admin created successfully.<br>";
    }
    
    echo "<br><strong>Admin Login Details:</strong><br>";
    echo "Email: admin@codelearn.com<br>";
    echo "Password: admin@123<br>";
    echo "<br>You can now login with these credentials.";
    
    // Also check and display current admin records
    echo "<br><br><strong>Current Admin Records:</strong><br>";
    $stmt = $pdo->query("SELECT admin_id, name, email, created_at FROM admin");
    $admins = $stmt->fetchAll();
    foreach ($admins as $admin) {
        echo "ID: {$admin['admin_id']}, Name: {$admin['name']}, Email: {$admin['email']}, Created: {$admin['created_at']}<br>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>