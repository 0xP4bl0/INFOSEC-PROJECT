<?php
include '../assets/auth/auth.php';
include '../config/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: /login/index.php");
    exit();
}

$user_id = $_SESSION['uid'];

$grade_query = "SELECT AVG(grade) AS gpa FROM grades WHERE student_id = ? AND status = 'Released'";
$stmt = $conn->prepare($grade_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$grade_data = $result->fetch_assoc();
$gpa = ($grade_data['gpa'] !== null) ? number_format($grade_data['gpa'], 2) : "0.00";
$stmt->close();

$enroll_query = "SELECT COUNT(*) AS total FROM enrollments WHERE user_id = ?";
$stmt2 = $conn->prepare($enroll_query);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
$enroll_data = $res2->fetch_assoc();
$total_subjects = $enroll_data['total'] ?? 0;
$stmt2->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
            <header>
                <h1 style="font-size: 42px; margin: 0; letter-spacing: -2px;">Student Overview</h1>
                <p style="color: #888;">Welcome back, Student ID: <?php echo htmlspecialchars($user_id); ?></p>
            </header>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 40px;">
                <div class="glass-card">
                    <h3 style="color: var(--neon-green); font-size: 12px; letter-spacing: 1px; text-transform: uppercase;">Academic Index (GPA)</h3>
                    <p style="font-size: 48px; font-weight: 800; margin: 20px 0; font-family: monospace;"><?php echo $gpa; ?></p>
                    <button class="neon-btn" onclick="window.location.href='grades.php'">View Analytics</button>
                </div>
                <div class="glass-card">
                    <h3 style="color: var(--neon-green); font-size: 12px; letter-spacing: 1px; text-transform: uppercase;">Total Enrolled</h3>
                    <p style="font-size: 48px; font-weight: 800; margin: 20px 0; font-family: monospace;"><?php echo $total_subjects; ?></p>
                    <button class="neon-btn" onclick="window.location.href='courses.php'">View Courses</button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>