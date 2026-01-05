<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'osas') {
    header("Location: /index.php");
    exit();
}

$students = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'student'")->fetch_assoc()['total'];
$teachers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'teacher'")->fetch_assoc()['total'];
$subjects = $conn->query("SELECT COUNT(*) AS total FROM subjects")->fetch_assoc()['total'];
$enrollments = $conn->query("SELECT COUNT(*) AS total FROM enrollments")->fetch_assoc()['total'];
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>OSAS Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/css/style.css">
<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 24px;
    margin-top: 30px;
}

.stat-card {
    background: var(--card-glass);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px;
    padding: 30px;
}

.stat-title {
    font-size: 14px;
    color: #777;
    margin-bottom: 10px;
}

.stat-value {
    font-size: 36px;
    font-weight: 800;
    color: var(--neon-green);
}

.quick-actions {
    margin-top: 40px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.action-card {
    background: var(--card-glass);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 18px;
    padding: 25px;
    cursor: pointer;
}

.action-card:hover {
    box-shadow: 0 0 14px var(--neon-green);
}

.action-card h3 {
    font-size: 16px;
    margin-bottom: 8px;
}

.action-card p {
    font-size: 14px;
    color: #888;
}
</style>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="portal-brand">OSAS Portal</div>
        <div class="nav-group">
            <a class="nav-link active" href="index.php">Dashboard</a>
            <a class="nav-link" href="students.php">Students</a>
            <a class="nav-link" href="teachers.php">Teachers</a>
            <a class="nav-link" href="subjects.php">Subjects</a>
            <a class="nav-link" href="enrollments.php">Enrollments</a>
            <a class="nav-link" href="grades.php">Grades</a>
        </div>
        <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
    </aside>

    <main class="main-view">
        <div class="container">
            <h1>OSAS Dashboard</h1>
            <p class="subtext">Overview of academic system</p>

            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-title">Total Students</div>
                    <div class="stat-value"><?= $students ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Total Teachers</div>
                    <div class="stat-value"><?= $teachers ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Subjects</div>
                    <div class="stat-value"><?= $subjects ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Enrollments</div>
                    <div class="stat-value"><?= $enrollments ?></div>
                </div>
            </div>

            <div class="quick-actions">
                <div class="action-card" onclick="location.href='students.php'">
                    <h3>Manage Students</h3>
                    <p>View, activate, or block students</p>
                </div>
                <div class="action-card" onclick="location.href='teachers.php'">
                    <h3>Manage Teachers</h3>
                    <p>Assign roles and review profiles</p>
                </div>
                <div class="action-card" onclick="location.href='subjects.php'">
                    <h3>Subjects</h3>
                    <p>Create and manage subject offerings</p>
                </div>
                <div class="action-card" onclick="location.href='grades.php'">
                    <h3>Release Grades</h3>
                    <p>Review and approve grade release requests</p>
                </div>
                <div class="action-card" onclick="location.href='reports.php'">
                    <h3>Reports</h3>
                    <p>Academic and enrollment summaries</p>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>