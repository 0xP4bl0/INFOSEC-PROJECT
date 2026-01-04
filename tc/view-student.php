<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit();
}
$student_id = $_GET['student_id'] ?? null;
$teacher_id = $_SESSION['user_id'];
if (!$student_id) die("Invalid student ID.");
$stmt = $conn->prepare("
    SELECT u.user_id, u.fullname, u.gender, u.email, COALESCE(u.status, 'Active') AS status, s.subject_name
    FROM users u
    INNER JOIN enrollments e ON u.user_id = e.user_id
    INNER JOIN subjects s ON e.subject_id = s.subject_id
    WHERE u.user_id = ? AND s.teacher_id = ?
");
$stmt->bind_param("ii", $student_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();
$conn->close();
if (!$student) die("Student not found or not enrolled in your class.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Student</title>
<link rel="stylesheet" href="/css/style.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="portal-brand">Teacher Portal</div>
        <div class="nav-group">
            <a class="nav-link" href="index.php">Dashboard</a>
            <a class="nav-link active" href="my_class.php">My Classes</a>
            <a class="nav-link" href="subjects.php">Manage Subjects</a>
            <a class="nav-link" href="students.php">Students</a>
            <a class="nav-link" href="#">Grades</a>
        <a class="nav-link" href="#">Announcements</a>
        </div>
        <button class="sign-out-btn" onclick="window.location.href='/logout.php'">Sign Out</button>
    </aside>

    <main class="main-view">
        <div class="container">
            <h1>Student Info</h1>
            <table class="modern-table">
                <tr><th>Student ID</th><td><?= htmlspecialchars($student['user_id']) ?></td></tr>
                <tr><th>Name</th><td><?= htmlspecialchars($student['fullname']) ?></td></tr>
                <tr><th>Gender</th><td><?= htmlspecialchars($student['gender']) ?></td></tr>
                <tr><th>Email</th><td><?= htmlspecialchars($student['email']) ?></td></tr>
                <tr><th>Status</th><td><?= htmlspecialchars($student['status']) ?></td></tr>
                <tr><th>Subject</th><td><?= htmlspecialchars($student['subject_name']) ?></td></tr>
            </table>
            <a href="my_class.php" class="neon-btn">Back to Classes</a>
        </div>
    </main>
</div>
</body>
</html>
