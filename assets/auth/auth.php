<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header("Location: /login/index.php");
    exit();
}

include __DIR__ . '/../../config/db.php';

$id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$stmt = $conn->prepare("
    SELECT COALESCE(status, 'Inactive') 
    FROM users 
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($status);
$stmt->fetch();
$stmt->close();
$conn->close();

if (($role === 'student' || $role === 'teacher') && $status !== 'Active') {
    session_unset();
    session_destroy();
    header("Location: /login/index.php?error=Your+account+has+been+deactivated+by+OSAS");
    exit();
}
