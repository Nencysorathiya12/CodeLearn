<?php
// setup_admin.php - Run this file once to create admin account
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codelearn_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Admin credentials
    $admin_name = "Admin User";
    $admin_email = "admin@codelearn.com";
    $admin_password = "admin123";  // Change this to your desired password
    
    // Hash the password
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT admin_id FROM admin WHERE email = ?");
    $stmt->execute([$admin_email]);
    
    if ($stmt->fetch()) {
        echo "Admin account already exists!<br>";
        echo "Email: " . $admin_email . "<br>";
        echo "Password: " . $admin_password . "<br>";
    } else {
        // Insert admin
        $stmt = $pdo->prepare("INSERT INTO admin (name, email, password) VALUES (?, ?, ?)");
        
        if ($stmt->execute([$admin_name, $admin_email, $hashed_password])) {
            echo "Admin account created successfully!<br>";
            echo "Email: " . $admin_email . "<br>";
            echo "Password: " . $admin_password . "<br>";
            echo "<br><strong>Please change the default password after first login!</strong>";
        } else {
            echo "Failed to create admin account!";
        }
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>