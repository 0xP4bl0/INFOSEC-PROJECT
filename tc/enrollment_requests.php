<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit();
}

$teacher_db_id = $_SESSION['user_id']; 
$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_enrollment'])) {
    $student_id = $_POST['student_user_id'];
    $subject_id = $_POST['subject_id'];

    $check = $conn->prepare("SELECT status FROM pending_enrollments WHERE student_id = ? AND subject_id = ? AND status = 'Pending'");
    $check->bind_param("si", $student_id, $subject_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $alert = "Request already exists for this subject.";
    } else {
        $stmt = $conn->prepare("INSERT INTO pending_enrollments (student_id, subject_id, teacher_id, status) VALUES (?, ?, ?, 'Pending')");
        $stmt->bind_param("sii", $student_id, $subject_id, $teacher_db_id);
        
        if ($stmt->execute()) {
            $alert = "Enrollment request sent successfully.";
        } else {
            $alert = "Error: " . $conn->error;
        }
        $stmt->close();
    }
    $check->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollment Requests | Teacher Portal</title>
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
            <a class="nav-link active" href="enrollment_requests.php">Enrollment Requests</a>
            <a class="nav-link" href="students.php">Students</a>
            <a class="nav-link" href="grades.php">Grades</a>
        </div>
        <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
    </aside>

    <main class="main-view">
        <div class="container">
            <h1>Enrollment Requests</h1>
            
            <?php if ($alert): ?>
                <div class="glass-card" style="border-left: 4px solid var(--neon-green); margin-bottom: 20px;">
                    <p style="margin: 0; color: #fff;"><?= htmlspecialchars($alert) ?></p>
                </div>
            <?php endif; ?>

            <div class="glass-card" style="margin-bottom: 30px;">
                <h3 style="color: #ff5555;">New Request (Irregular/Transferee)</h3>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Type</th>
                            <th>Subject to Request</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $irregulars = mysqli_query($conn, "
                            SELECT u.user_id, u.fullname, ss.status
                            FROM users u
                            JOIN student_status ss ON u.user_id = ss.user_id
                            WHERE u.role = 'student' AND ss.status IN ('irregular', 'transferee')
                            ORDER BY u.fullname
                        ");

                        while ($row = mysqli_fetch_assoc($irregulars)) {
                            $sid = $row['user_id'];
                            $sub_stmt = $conn->prepare("
                                SELECT subject_id, subject_name FROM subjects 
                                WHERE teacher_id = ?
                                AND subject_id NOT IN (SELECT subject_id FROM enrollments WHERE user_id = ?)
                                AND subject_id NOT IN (SELECT subject_id FROM pending_enrollments WHERE student_id = ? AND status = 'Pending')
                            ");
                            $sub_stmt->bind_param("iss", $teacher_db_id, $sid, $sid);
                            $sub_stmt->execute();
                            $subs = $sub_stmt->get_result();

                            if ($subs->num_rows > 0) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($sid) . "</td>
                                    <td>" . htmlspecialchars($row['fullname']) . "</td>
                                    <td>" . ucfirst($row['status']) . "</td>
                                    <td>
                                        <form method='POST' style='display:flex; gap:10px;'>
                                            <input type='hidden' name='request_enrollment' value='1'>
                                            <input type='hidden' name='student_user_id' value='{$sid}'>
                                            <select name='subject_id' required class='modern-select' style='background:rgba(0,0,0,0.5); color:#fff;'>
                                                <option value='' disabled selected>Select Your Subject</option>";
                                                while($s = $subs->fetch_assoc()) {
                                                    echo "<option value='{$s['subject_id']}'>{$s['subject_name']}</option>";
                                                }
                                echo "      </select>
                                            <button type='submit' class='neon-btn'>Send</button>
                                        </form>
                                    </td>
                                </tr>";
                            }
                            $sub_stmt->close();
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="glass-card">
                <h3>Sent Requests History</h3>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Date Sent</th>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $history = mysqli_query($conn, "
                            SELECT p.request_date, u.fullname, s.subject_name, p.status 
                            FROM pending_enrollments p
                            JOIN users u ON p.student_id = u.user_id
                            JOIN subjects s ON p.subject_id = s.subject_id
                            WHERE p.teacher_id = '$teacher_db_id'
                            ORDER BY p.request_date DESC
                        ");
                        while($h = mysqli_fetch_assoc($history)) {
                            $statusColor = ($h['status'] == 'Approved') ? '#39ff14' : (($h['status'] == 'Pending') ? '#ffa500' : '#ff5555');
                            echo "<tr>
                                <td>" . date('M d, Y', strtotime($h['request_date'])) . "</td>
                                <td>" . htmlspecialchars($h['fullname']) . "</td>
                                <td>" . htmlspecialchars($h['subject_name']) . "</td>
                                <td style='color: $statusColor; font-weight: bold;'>{$h['status']}</td>
                            </tr>";
                        }
                        if(mysqli_num_rows($history) == 0) echo "<tr><td colspan='4' style='text-align:center;'>No history found.</td></tr>";
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>