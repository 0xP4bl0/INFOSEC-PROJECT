<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ? AND teacher_id = ?");
    $stmt->bind_param("ii", $id, $teacher_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: subjects.php");
exit();
