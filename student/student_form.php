<?php
include '../assets/auth/auth.php';
include '../config/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: /login/index.php");
    exit();
}

$session_id = $_SESSION['uid'];

$query = "SELECT user_id, fullname, email, gender FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User profile not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="app-shell">
    <nav class="sidebar">
        <div class="portal-brand">STUDENT PORTAL</div>
        <div class="nav-group">
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="courses.php" class="nav-link">My Courses</a>
            <a href="grades.php" class="nav-link">Grades</a>
            <a href="student_form.php" class="nav-link active">Profile</a>
        </div>
        <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
    </nav>
    <main class="main-view">
        <div class="container">
            <h1>Student Profile</h1>
            <div class="glass-card">
                <h2 style="margin: 0; font-size: 28px; text-transform: uppercase;">
                    <?php echo htmlspecialchars($user['fullname']); ?>
                </h2>
                <p style="color: var(--neon-green); font-weight: 700;">
                    ID: <?php echo htmlspecialchars($user['user_id']); ?>
                </p>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px; border-top: 1px solid #333; padding-top: 30px;">
                    <div>
                        <p style="color: #666; font-size: 12px; letter-spacing: 1px;">GENDER</p>
                        <p style="font-size: 18px; font-weight: 600;">
                            <?php echo htmlspecialchars($user['gender']); ?>
                        </p>
                    </div>
                    <div>
                        <p style="color: #666; font-size: 12px; letter-spacing: 1px;">EMAIL ADDRESS</p>
                        <p style="font-size: 18px; font-weight: 600;">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>