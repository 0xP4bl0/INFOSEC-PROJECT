<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT u.user_id, u.fullname, u.gender, COALESCE(ss.status, 'Active') AS status, s.subject_name
    FROM users u
    INNER JOIN enrollments e ON u.user_id = e.user_id
    INNER JOIN subjects s ON e.subject_id = s.subject_id
    INNER JOIN student_status ss ON u.user_id = ss.user_id
    WHERE s.teacher_id = ?
    ORDER BY s.subject_name, u.fullname
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Class</title>
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
            <h1>My Classes</h1>
            <p class="subtext">Students enrolled in your subjects</p>

            <div class="glass-card">
                <h3>Student List</h3>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>Subject</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $index => $student): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($student['user_id']) ?></td>
                                <td><?= htmlspecialchars($student['fullname']) ?></td>
                                <td><?= htmlspecialchars($student['gender']) ?></td>
                                <td><?= htmlspecialchars($student['status']) ?></td>
                                <td><?= htmlspecialchars($student['subject_name']) ?></td>
                                <td>
                                    <a href="view-student.php?student_id=<?= urlencode($student['user_id']) ?>" class="neon-btn">View</a>
                                    <a href="grade-student.php?student_id=<?= urlencode($student['user_id']) ?>" class="neon-btn">Grade</a>
                                    <a href="message-student.php?student_id=<?= urlencode($student['user_id']) ?>" class="neon-btn">Message</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align:center;">No students enrolled in your subjects.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
