<?php
header('Content-Type: application/json');
include "config.php"; // Database connection

$query = strtolower($_GET['q'] ?? '');
$result = [];

if ($query) {
    $stmt = $conn->prepare("SELECT * FROM courses WHERE LOWER(title) LIKE ?");
    $stmt->execute(["%$query%"]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($courses as $course) {
        $result[] = [
            "name" => $course['title'],
            "course_id" => $course['course_id'],
            "url" => "lessons.php?course_id=" . $course['course_id']
        ];
    }
}

echo json_encode($result);
