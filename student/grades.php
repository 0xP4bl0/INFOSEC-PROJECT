<?php
include '../assets/auth/auth.php';
include '../config/db.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: /login/index.php");
    exit();
}

$student_uid = $_SESSION['uid']; 

$query = "
    SELECT s.subject_code, s.subject_name, g.grade, g.status
    FROM grades g
    JOIN subjects s ON g.subject_id = s.subject_id
    WHERE g.student_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $student_uid);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grades</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="app-shell">
    <nav class="sidebar">
        <div class="portal-brand">STUDENT PORTAL</div>
        <div class="nav-group">
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="courses.php" class="nav-link">My Courses</a>
            <a href="grades.php" class="nav-link active">Grades</a>
            <a href="student_form.php" class="nav-link">Profile</a>
        </div>
        <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
    </nav>
    <main class="main-view">
        <div class="container">
            <h1>Academic Grades</h1>
            <div class="glass-card" style="padding: 0; overflow: hidden;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Description</th>
                            <th style="text-align: center;">Grade</th>
                            <th style="text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): 
                                $statusColor = ($row['grade'] <= 3.00) ? 'var(--neon-green)' : '#ff4444';
                                $displayStatus = ($row['grade'] <= 3.00) ? 'PASSED' : 'FAILED';
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                    <td style="text-align: center; font-weight: bold;">
                                        <?php echo number_format($row['grade'], 2); ?>
                                    </td>
                                    <td style="text-align: center; color: <?php echo $statusColor; ?>; font-weight: bold;">
                                        <?php echo htmlspecialchars($row['status']); ?> (<?php echo $displayStatus; ?>)
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 20px;">No grades released yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>