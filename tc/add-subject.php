<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = trim($_POST['subject_code'] ?? '');
    $subject_name = trim($_POST['subject_name'] ?? '');

    if (empty($subject_code) || empty($subject_name)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name, teacher_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $subject_code, $subject_name, $teacher_id);

        if ($stmt->execute()) {
            $success = "Subject added successfully!";
        } else {
            $error = "Error adding subject: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Subject | Teacher Portal</title>
<link rel="stylesheet" href="/css/style.css">
</head>
<body class="app-shell">

<aside class="sidebar">
    <div class="portal-brand">Teacher Portal</div>
    <div class="nav-group">
            <a class="nav-link" href="index.php">Dashboard</a>
            <a class="nav-link" href="my_class.php">My Classes</a>
            <a class="nav-link active" href="subjects.php">Manage Subjects</a> 
            <a class="nav-link" href="#">Students</a>
            <a class="nav-link" href="#">Grades</a>
            <a class="nav-link" href="#">Announcements</a>
    </div>
    <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
</aside>

<main class="main-view">
    <div class="container">
        <div class="glass-card" style="max-width:500px;">
            <h2>Add Subject</h2>

            <?php if($error): ?>
                <p style="color:red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <?php if($success): ?>
                <p style="color:green;"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <form method="POST" action="">
                <label>Subject Code</label>
                <input type="text" name="subject_code" placeholder="e.g. ITEC101" required style="width:100%; padding:12px; margin-bottom:15px;">

                <label>Subject Name</label>
                <input type="text" name="subject_name" placeholder="e.g. Introduction to IT" required style="width:100%; padding:12px; margin-bottom:15px;">

                <button type="submit" class="neon-btn" style="width:100%;">Add Subject</button>
            </form>
        </div>
    </div>
</main>

</body>
</html>
