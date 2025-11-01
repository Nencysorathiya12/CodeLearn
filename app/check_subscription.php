<?php
/**
 * Check Subscription Status
 * This file checks if a user's paid subscription is still active
 * Run this as a cron job daily or check on user login
 */

function checkUserSubscription($pdo, $userId) {
    try {
        // Get user's current plan
        $stmt = $pdo->prepare("SELECT plan FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || $user['plan'] === 'free') {
            return true; // Free plan never expires
        }
        
        // Check if there's an active payment record
        $stmt = $pdo->prepare("
            SELECT payment_id, end_date, status 
            FROM payment 
            WHERE user_id = ? 
            AND plan = ? 
            AND status = 'completed' 
            AND end_date >= CURDATE() 
            ORDER BY end_date DESC 
            LIMIT 1
        ");
        $stmt->execute([$userId, $user['plan']]);
        $activePayment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$activePayment) {
            // No active subscription found, downgrade to free
            $updateStmt = $pdo->prepare("UPDATE users SET plan = 'free' WHERE user_id = ?");
            $updateStmt->execute([$userId]);
            return false;
        }
        
        return true; // Subscription is active
        
    } catch (Exception $e) {
        error_log("Subscription check error: " . $e->getMessage());
        return false;
    }
}

// If called directly (for cron job)
if (php_sapi_name() === 'cli') {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "codelearn_db";
    
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get all users with paid plans
        $stmt = $pdo->query("SELECT user_id, plan FROM users WHERE plan IN ('pro', 'team')");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $downgraded = 0;
        foreach ($users as $user) {
            $isActive = checkUserSubscription($pdo, $user['user_id']);
            if (!$isActive) {
                $downgraded++;
                echo "User ID {$user['user_id']} downgraded to free (subscription expired)\n";
            }
        }
        
        echo "Subscription check completed. {$downgraded} users downgraded.\n";
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>