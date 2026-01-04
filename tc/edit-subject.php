<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit();
}

$id = $_GET['id'] ?? null;
$teacher_id = $_SESSION['user_id'];

if (!$id) {
    header("Location: subjects.php");
    exit();
}

$stmt = $conn->prepare("SELECT subject_code, subject_name FROM subjects WHERE subject_id = ? AND teacher_id = ?");
$stmt->bind_param("ii", $id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$subject = $result->fetch_assoc();

if (!$subject) {
    header("Location: subjects.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);
    $update = $conn->prepare("UPDATE subjects SET subject_code = ?, subject_name = ? WHERE id = ? AND teacher_id = ?");
    $update->bind_param("ssii", $subject_code, $subject_name, $id, $teacher_id);
    $update->execute();
    header("Location: subjects.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Subject | Teacher Portal</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body class="app-shell">

<aside class="sidebar">
    <div class="portal-brand">Teacher Portal</div>
    <div class="nav-group">
        <a class="nav-link" href="index.php">Dashboard</a>
        <a class="nav-link" href="my_class.php">My Classes</a>
        <a class="nav-link active" href="subjects.php">Manage Subjects</a>
        <a class="nav-link" href="students.php">Students</a>
        <a class="nav-link" href="#">Grades</a>
        <a class="nav-link" href="#">Announcements</a>
    </div>
    <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
</aside>

<main class="main-view">
    <div class="container">
        <h1>Edit Subject</h1>
        <p class="subtext">Update the details of your subject</p>
        <form method="POST" class="glass-card">
            <label>Subject Code</label>
            <input type="text" name="subject_code" value="<?= htmlspecialchars($subject['subject_code']) ?>" required>
            <label>Subject Name</label>
            <input type="text" name="subject_name" value="<?= htmlspecialchars($subject['subject_name']) ?>" required>
            <div style="margin-top:20px;">
                <button type="submit" class="neon-btn">Update Subject</button>
                <a href="subjects.php" class="neon-btn" style="margin-left:10px;">Cancel</a>
            </div>
        </form>
    </div>
</main>

</body>
</html>
