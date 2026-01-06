<?php
session_start();
include_once '../assets/auth/auth.php';
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit();
}

$teacher_db_id = $_SESSION['user_id'];

$query = "
    SELECT 
        u.user_id AS student_num, 
        u.fullname, 
        s.subject_name, 
        g.grade, 
        g.grade_status,
        g.status AS release_status
    FROM grades g
    JOIN users u ON g.student_id = u.user_id
    JOIN subjects s ON g.subject_id = s.subject_id
    WHERE g.teacher_id = ? 
    AND g.status != 'Rejected'
    ORDER BY s.subject_name ASC, u.fullname ASC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_db_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Grades | Teacher Portal</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="portal-brand">Teacher Portal</div>
        <div class="nav-group">
            <a class="nav-link" href="index.php">Dashboard</a>
            <a class="nav-link" href="my_class.php">My Classes</a>
            <a class="nav-link" href="subjects.php">Manage Subjects</a>
            <a class="nav-link" href="enrollment_requests.php">Enrollment Requests</a>
            <a class="nav-link" href="students.php">Students</a>
            <a class="nav-link active" href="grades.php">Grades</a>
        </div>
        <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
    </aside>

    <main class="main-view">
        <div class="container">
            <h1>Student Grade Records</h1>
            <div class="glass-card">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Subject</th>
                            <th>Grade</th>
                            <th>Result</th>
                            <th>Release Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['student_num']) ?></td>
                                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                                    <td><?= htmlspecialchars($row['subject_name']) ?></td>
                                    <td style="font-weight: bold;"><?= number_format($row['grade'], 2) ?></td>
                                    <td>
                                        <?php $color = ($row['grade_status'] === 'Passed') ? '#39ff14' : '#ff5555'; ?>
                                        <span style="color: <?= $color ?>; font-weight: bold;">
                                            <?= htmlspecialchars($row['grade_status'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge">
                                            <?= htmlspecialchars($row['release_status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;">No grades found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>