<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT subject_id, subject_code, subject_name, created_at FROM subjects WHERE teacher_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Subjects | Teacher Portal</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="app-shell">

<aside class="sidebar">
    <div class="portal-brand">Teacher Portal</div>
    <div class="nav-group">
            <a class="nav-link" href="index.php">Dashboard</a>
            <a class="nav-link" href="my_class.php">My Classes</a>
            <a class="nav-link active" href="subjects.php">Manage Subjects</a> 
            <a class="nav-link" href="enrollment_requests.php">Enrollment Requests</a>
            <a class="nav-link" href="students.php">Students</a>
            <a class="nav-link" href="grades.php">Grades</a>
        </div>
    <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
</aside>

<main class="main-view">
    <div class="container">

        <h1>Assigned Subjects</h1>
        <p class="subtext">View subjects assigned to you by OSAS</p>

        <div class="glass-card">
            <h2>My Subjects</h2>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Date Assigned</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($subjects)): ?>
                        <?php foreach($subjects as $index => $subject): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($subject['subject_code']) ?></td>
                            <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                            <td><?= htmlspecialchars(date("M d, Y", strtotime($subject['created_at']))) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">No subjects assigned yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

</body>
</html>