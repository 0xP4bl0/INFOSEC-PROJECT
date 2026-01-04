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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'];
    $grade = $_POST['grade'];
    $stmt = $conn->prepare("
        INSERT INTO grades (user_id, subject_id, grade)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE grade = ?
    ");
    $stmt->bind_param("iidd", $student_id, $subject_id, $grade, $grade);
    if ($stmt->execute()) $success = "Grade submitted successfully.";
    else $error = "Failed to submit grade.";
    $stmt->close();
}
$stmt = $conn->prepare("
    SELECT s.subject_id, s.subject_name
    FROM subjects s
    INNER JOIN enrollments e ON s.subject_id = e.subject_id
    WHERE e.user_id = ? AND s.teacher_id = ?
");
$stmt->bind_param("ii", $student_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$subjects = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Grade Student</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/css/style.css">
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
            <h1>Grade Student</h1>
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
            <form method="POST" class="glass-card">
                <label for="subject_id">Select Subject:</label>
                <select name="subject_id" required>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['subject_id'] ?>"><?= htmlspecialchars($subject['subject_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="grade">Grade:</label>
                <input type="number" name="grade" min="0" max="100" step="0.01" required>
                <button type="submit">Submit Grade</button>
            </form>
            <a href="my_class.php" class="neon-btn">Back to Classes</a>
        </div>
    </main>
</div>
</body>
</html>
