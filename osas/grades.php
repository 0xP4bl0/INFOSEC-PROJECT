<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'osas') {
    header("Location: /index.php");
    exit();
}

$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $grade_id = $_POST['grade_id'];
    $action = $_POST['action'];
    $new_status = ($action === 'approve') ? 'Released' : 'Rejected';

    $stmt = $conn->prepare("UPDATE grades SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $grade_id);
    
    if ($stmt->execute()) {
        $alert = "Grade status updated to: " . $new_status;
    }
    $stmt->close();
}

$pending_query = "
    SELECT 
        g.id, 
        g.grade, 
        g.status, 
        g.created_at, 
        u_s.fullname AS student_name, 
        g.student_id AS display_student_id,
        u_t.fullname AS teacher_name,
        s.subject_name, 
        s.subject_code
    FROM grades g
    INNER JOIN users u_s ON g.student_id = u_s.user_id
    INNER JOIN users u_t ON g.teacher_id = u_t.id
    INNER JOIN subjects s ON g.subject_id = s.subject_id
    WHERE g.status = 'Pending'
    ORDER BY g.created_at ASC
";
$pending_grades = $conn->query($pending_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Management | OSAS Portal</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="app-shell">

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
        <h1>Grade Release Requests</h1>
        
        <?php if ($alert): ?>
            <div class="glass-card" style="border-left: 4px solid var(--neon-green); margin-bottom: 20px;">
                <p style="margin: 0; color: #fff;"><?= htmlspecialchars($alert) ?></p>
            </div>
        <?php endif; ?>

        <div class="glass-card">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Grade</th>
                        <th>Date Requested</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pending_grades && $pending_grades->num_rows > 0): ?>
                        <?php while($row = $pending_grades->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($row['student_name']) ?></strong><br>
                                <small style="color: #888;"><?= htmlspecialchars($row['display_student_id']) ?></small>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['subject_code']) ?><br>
                                <small style="color: #888;"><?= htmlspecialchars($row['subject_name']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($row['teacher_name']) ?></td>
                            <td>
                                <span style="color: var(--neon-green); font-weight: bold;">
                                    <?= number_format($row['grade'], 2) ?>
                                </span>
                            </td>
                            <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
                            <td>
                                <form method="POST" style="display: flex; gap: 8px;">
                                    <input type="hidden" name="grade_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="action" value="approve" class="neon-btn" style="padding: 5px 10px; font-size: 11px;">Approve</button>
                                    <button type="submit" name="action" value="reject" class="neon-btn" style="padding: 5px 10px; font-size: 11px; border-color: #ff5555; color: #ff5555;">Reject</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 30px; color: #888;">No pending grade requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

</body>
</html>