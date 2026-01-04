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
        <a class="nav-link" href="students.php">Students</a>
        <a class="nav-link" href="#">Grades</a>
        <a class="nav-link" href="#">Announcements</a>
    </div>
    <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
</aside>

<main class="main-view">
    <div class="container">

        <h1>Manage Subjects</h1>
        <p class="subtext">View your current subjects</p>

        <div class="glass-card">
            <h2>My Subjects</h2>
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Date Added</th>
                        <th>Action</th>
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
                            <td>
                                <a href="edit-subject.php?id=<?= $subject['subject_id'] ?>" class="neon-btn">Edit</a>
                                <a href="delete-subject.php?id=<?= $subject['subject_id'] ?>" class="neon-btn" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">No subjects found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div style="margin-top:20px; text-align:right;">
                <a href="add-subject.php" class="neon-btn" style="padding:10px 20px;">Add Subject</a>
            </div>
        </div>

    </div>
</main>

</body>
</html>
