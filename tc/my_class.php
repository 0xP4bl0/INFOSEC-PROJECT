<?php
session_start();
include_once '../assets/auth/auth.php';
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit();
}

$teacher_db_id = $_SESSION['user_id']; 

$stmt = $conn->prepare("
    SELECT 
        u.user_id, 
        u.fullname, 
        u.gender, 
        u.status, 
        s.subject_id,
        s.subject_name,
        COALESCE(ss.status, 'Regular') AS enrollment_type,
        MAX(g.status) AS grade_release_status
    FROM enrollments e
    JOIN users u ON e.user_id = u.user_id
    JOIN subjects s ON e.subject_id = s.subject_id
    LEFT JOIN student_status ss ON u.user_id = ss.user_id
    LEFT JOIN grades g ON (u.user_id = g.student_id AND s.subject_id = g.subject_id)
    WHERE s.teacher_id = ?
    GROUP BY u.user_id, s.subject_id
    ORDER BY s.subject_name, u.fullname
");

$stmt->bind_param("i", $teacher_db_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Class | Teacher Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="portal-brand">Teacher Portal</div>
        <div class="nav-group">
            <a class="nav-link" href="index.php">Dashboard</a>
            <a class="nav-link active" href="my_class.php">My Classes</a>
            <a class="nav-link" href="subjects.php">Manage Subjects</a> 
            <a class="nav-link" href="enrollment_requests.php">Enrollment Requests</a>
            <a class="nav-link" href="students.php">Students</a>
            <a class="nav-link" href="grades.php">Grades</a>
        </div>
        <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
    </aside>

    <main class="main-view">
        <div class="container">
            <h1>My Classes</h1>
            <div class="glass-card">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $index => $student): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($student['user_id']) ?></td>
                                <td><?= htmlspecialchars($student['fullname']) ?></td>
                                <td><?= htmlspecialchars($student['gender']) ?></td>
                                <td><span class="status-badge"><?= htmlspecialchars($student['enrollment_type']) ?></span></td>
                                <td><?= htmlspecialchars($student['subject_name']) ?></td>
                                <td style="display: flex; gap: 10px;">
                                    <a href="view-student.php?student_id=<?= urlencode($student['user_id']) ?>" class="neon-btn" style="font-size: 11px; padding: 5px 10px;">View</a>
                                    
                                    <?php 
                                        $status = $student['grade_release_status'];
                                        $isRejected = ($status === 'Rejected');
                                        $notGraded = (is_null($status) || $status === '');
                                    ?>

                                    <?php if ($notGraded || $isRejected): ?>
                                        <a href="grade-student.php?student_id=<?= urlencode($student['user_id']) ?>&subject_id=<?= $student['subject_id'] ?>" class="neon-btn" style="font-size: 11px; padding: 5px 10px;">Grade</a>
                                    <?php else: ?>
                                        <span style="color: var(--neon-green); font-size: 11px; font-weight: bold; align-self: center;">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align:center;">No students enrolled.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
<?php $conn->close(); ?>