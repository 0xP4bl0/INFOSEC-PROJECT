<?php
include '../assets/auth/auth.php';
include '../config/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: /login/index.php");
    exit();
}

$user_id = $_SESSION['uid'];

$query = "
    SELECT s.subject_code, s.subject_name
    FROM enrollments e
    JOIN subjects s ON e.subject_id = s.subject_id
    WHERE e.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id); 
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Courses</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="app-shell">
    <nav class="sidebar">
        <div class="portal-brand">STUDENT PORTAL</div>
        <div class="nav-group">
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="courses.php" class="nav-link active">My Courses</a>
            <a href="grades.php" class="nav-link">Grades</a>
            <a href="student_form.php" class="nav-link">Profile</a>
        </div>
        <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
    </nav>
    <main class="main-view">
        <div class="container">
            <h1>My Enrolled Courses</h1>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; margin-top: 30px;">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($course = $result->fetch_assoc()): ?>
                        <div class="glass-card" style="border-left: 4px solid var(--neon-green);">
                            <span style="color: var(--neon-green); font-weight: 700;"><?php echo htmlspecialchars($course['subject_code']); ?></span>
                            <h3 style="margin: 10px 0;"><?php echo htmlspecialchars($course['subject_name']); ?></h3>
                            <p style="color: #666; font-size: 14px;">Lecture & Laboratory</p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="glass-card">
                        <p>No enrolled courses found for User ID: <?php echo htmlspecialchars($user_id); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>