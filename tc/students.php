<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit();
}

$teacher_session_id = $_SESSION['uid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Directory | Teacher Portal</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="portal-brand">Teacher Portal</div>
        <div class="nav-group">
            <a class="nav-link" href="index.php">Dashboard</a>
            <a class="nav-link" href="my_class.php">My Classes</a>
            <a class="nav-link" href="subjects.php">Manage Subjects</a>
            <a class="nav-link" href="enrollment_requests.php">Enrollment Requests</a>
            <a class="nav-link active" href="students.php">Students</a>
            <a class="nav-link" href="grades.php">Grades</a>
        </div>
        <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
    </aside>

    <main class="main-view">
        <div class="container">
            <div class="glass-card">
                <h2 style="color: var(--neon-green); margin-bottom: 15px;">Students in My Subjects</h2>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Status</th>
                            <th>My Subjects Handled</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "
                            SELECT u.user_id, u.fullname, 
                                   COALESCE(ss.status, 'Regular') AS student_type, 
                                   GROUP_CONCAT(s.subject_name SEPARATOR ', ') AS subjects
                            FROM users u
                            INNER JOIN enrollments e ON u.user_id = e.user_id
                            INNER JOIN subjects s ON e.subject_id = s.subject_id
                            LEFT JOIN student_status ss ON u.user_id = ss.user_id
                            WHERE u.role = 'student' 
                            AND s.teacher_id = (SELECT id FROM users WHERE user_id = ?)
                            GROUP BY u.user_id
                            ORDER BY u.fullname
                        ";

                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("s", $teacher_session_id);
                        $stmt->execute();
                        $enrolled = $stmt->get_result();

                        while ($row = $enrolled->fetch_assoc()) {
                            $typeClass = strtolower($row['student_type']);
                            echo "<tr>
                                <td>" . htmlspecialchars($row['user_id']) . "</td>
                                <td>" . htmlspecialchars($row['fullname']) . "</td>
                                <td><span class='status-badge {$typeClass}'>" . ucfirst($row['student_type']) . "</span></td>
                                <td style='color: var(--neon-green); font-weight: bold;'>" . htmlspecialchars($row['subjects']) . "</td>
                            </tr>";
                        }

                        if ($enrolled->num_rows == 0) {
                            echo "<tr><td colspan='4' style='text-align:center;'>No students enrolled in your subjects</td></tr>";
                        }
                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>