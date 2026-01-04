<?php
include '../config/db.php';
include_once '../assets/auth/auth.php';
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: /index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Dashboard</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="app-shell">
    <nav class="sidebar">
        <div class="portal-brand">STUDENT PORTAL</div>
        <div class="nav-group">
            <a href="dashboard.php" class="nav-link active">Dashboard</a>
            <a href="courses.php" class="nav-link">My Courses</a>
            <a href="grades.php" class="nav-link">Grades</a>
            <a href="student_form.php" class="nav-link">Profile</a>
        </div>
        <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
    </nav>
    <main class="main-view">
        <div class="container">
            <h1 style="font-size: 42px; margin: 0; letter-spacing: -2px;">Student Overview</h1>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; margin-top: 40px;">
                <div class="glass-card">
                    <h3 style="color: var(--neon-green); font-size: 12px; letter-spacing: 1px;">ACADEMIC INDEX</h3>
                    <p style="font-size: 36px; font-weight: 800; margin: 20px 0;">1.88 GPA</p>
                    <button class="neon-btn" onclick="window.location.href='grades.php'">View Analytics</button>
                </div>
                <div class="glass-card">
                    <h3 style="color: var(--neon-green); font-size: 12px; letter-spacing: 1px;">TOTAL ENROLLED</h3>
                    <p style="font-size: 36px; font-weight: 800; margin: 20px 0;">8 Subjects</p>
                    <button class="neon-btn" onclick="window.location.href='courses.php'">View Courses</button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>