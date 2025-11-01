<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codelearn_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

try {
    $userId = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("
        SELECT 
            c.certificate_id,
            c.certificate_file,
            c.certificate_number,
            c.issued_date,
            co.title as course_title
        FROM certificates c
        INNER JOIN courses co ON c.course_id = co.course_id
        WHERE c.user_id = ?
        ORDER BY c.issued_date DESC
    ");
    
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $certificates = [];
    while ($row = $result->fetch_assoc()) {
        $row['issued_date'] = date('M d, Y', strtotime($row['issued_date']));
        $certificates[] = $row;
    }
    
    echo json_encode($certificates);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([]);
}

$conn->close();
?>