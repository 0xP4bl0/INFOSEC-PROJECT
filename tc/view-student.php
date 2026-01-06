<?php
session_start();
include_once '../assets/auth/auth.php';
include '../config/db.php';

function validate_param($data, $name) {
    if (preg_match("/[';]|--|ORDER|UNION|SELECT|DROP/i", $data)) {
        die("Security Alert: SQL INJECTION detected in parameter '$name'");
    }
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit();
}

$student_user_id = $_GET['student_id'] ?? null;
if ($student_user_id) {
    validate_param($student_user_id, 'student_id');
} else {
    die("Invalid student ID.");
}

$teacher_db_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT u.user_id, u.fullname, u.gender, u.email, u.status, s.subject_name
    FROM users u
    JOIN enrollments e ON u.user_id = e.user_id
    JOIN subjects s ON e.subject_id = s.subject_id
    WHERE u.user_id = ? AND s.teacher_id = ?
");

$stmt->bind_param("si", $student_user_id, $teacher_db_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student not found or not enrolled in your class.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student | Teacher Portal</title>
    <link rel="stylesheet" href="/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="app-shell">
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
            <h1>Student Profile</h1>
            <div class="glass-card">
                <table class="modern-table">
                    <tr>
                        <th style="width: 30%; text-align: left;">Student ID</th>
                        <td><?= htmlspecialchars($student['user_id']) ?></td>
                    </tr>
                    <tr>
                        <th style="text-align: left;">Full Name</th>
                        <td><?= htmlspecialchars($student['fullname']) ?></td>
                    </tr>
                    <tr>
                        <th style="text-align: left;">Gender</th>
                        <td><?= htmlspecialchars($student['gender']) ?></td>
                    </tr>
                    <tr>
                        <th style="text-align: left;">Email Address</th>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                    </tr>
                    <tr>
                        <th style="text-align: left;">Account Status</th>
                        <td>
                            <span class="status-badge" style="background: rgba(57, 255, 20, 0.1); color: #39ff14;">
                                <?= htmlspecialchars($student['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th style="text-align: left;">Enrolled Subject</th>
                        <td><?= htmlspecialchars($student['subject_name']) ?></td>
                    </tr>
                </table>
                <div style="margin-top: 20px;">
                    <a href="my_class.php" class="neon-btn" style="text-decoration: none;">Back to Classes</a>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
<?php $conn->close(); ?>