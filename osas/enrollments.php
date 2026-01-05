<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'osas') {
    header("Location: /index.php");
    exit();
}

$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        if (isset($_POST['action']) && $_POST['action'] === 'approve_request') {
            $pending_id = $_POST['pending_id'];
            $student_db_id = $_POST['student_id']; 
            $subject_id = $_POST['subject_id'];

            $uStmt = $conn->prepare("SELECT user_id, status FROM users WHERE id = ?");
            $uStmt->bind_param("i", $student_db_id);
            $uStmt->execute();
            $user_data = $uStmt->get_result()->fetch_assoc();
            $uStmt->close();

            if (!$user_data || $user_data['status'] !== 'Active') {
                throw new Exception("Student account is inactive.");
            }

            $actual_user_id = $user_data['user_id'];

            $check = $conn->prepare("SELECT 1 FROM enrollments WHERE user_id = ? AND subject_id = ?");
            $check->bind_param("si", $actual_user_id, $subject_id);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                throw new Exception("Student is already enrolled in this subject.");
            }
            $check->close();

            $stmt = $conn->prepare("INSERT INTO enrollments (user_id, subject_id) VALUES (?, ?)");
            $stmt->bind_param("si", $actual_user_id, $subject_id);
            $stmt->execute();

            $upd = $conn->prepare("UPDATE pending_enrollments SET status = 'Approved' WHERE id = ?");
            $upd->bind_param("i", $pending_id);
            $upd->execute();

            $conn->commit();
            $alert = "Enrollment approved and recorded.";
        } 
        elseif (isset($_POST['student_id']) && isset($_POST['subject_id'])) {
            $student_db_id = $_POST['student_id'];
            $subject_id = $_POST['subject_id'];

            $uStmt = $conn->prepare("SELECT user_id FROM users WHERE id = ? AND status = 'Active'");
            $uStmt->bind_param("i", $student_db_id);
            $uStmt->execute();
            $user_data = $uStmt->get_result()->fetch_assoc();
            $uStmt->close();

            if (!$user_data) {
                throw new Exception("Student not found or inactive.");
            }

            $actual_user_id = $user_data['user_id'];

            $check = $conn->prepare("SELECT 1 FROM enrollments WHERE user_id = ? AND subject_id = ?");
            $check->bind_param("si", $actual_user_id, $subject_id);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                throw new Exception("Student is already enrolled in this subject.");
            }
            $check->close();

            $stmt = $conn->prepare("INSERT INTO enrollments (user_id, subject_id) VALUES (?, ?)");
            $stmt->bind_param("si", $actual_user_id, $subject_id);
            $stmt->execute();

            $conn->commit();
            $alert = "Manual enrollment successful.";
        }
    } catch (Exception $e) {
        $conn->rollback();
        $alert = $e->getMessage();
    }
}

$pending_requests = $conn->query("
    SELECT p.id, u.fullname AS s_name, u.id AS student_db_id, s.subject_name AS sub_name, p.subject_id, t.fullname AS t_name
    FROM pending_enrollments p
    JOIN users u ON p.student_id = u.user_id
    JOIN subjects s ON p.subject_id = s.subject_id
    JOIN users t ON p.teacher_id = t.id
    WHERE p.status = 'Pending'
")->fetch_all(MYSQLI_ASSOC);

$students = $conn->query("
    SELECT u.id, u.fullname 
    FROM users u
    LEFT JOIN student_status ss ON u.user_id = ss.user_id
    WHERE u.role = 'student' AND u.status = 'Active' 
    AND (ss.status = 'Regular' OR ss.status IS NULL)
    ORDER BY u.fullname
")->fetch_all(MYSQLI_ASSOC);

$subjectsRaw = $conn->query("
    SELECT s.subject_id, s.subject_name, COALESCE(u.fullname, 'Unassigned') AS teacher 
    FROM subjects s 
    LEFT JOIN users u ON s.teacher_id = u.id 
    ORDER BY teacher, s.subject_name
")->fetch_all(MYSQLI_ASSOC);

$subjects = [];
foreach ($subjectsRaw as $row) { $subjects[$row['teacher']][] = $row; }

$enrollments = $conn->query("
    SELECT u.fullname AS student, s.subject_name, COALESCE(t.fullname, 'Unassigned') AS teacher, COALESCE(st.status, 'Regular') AS student_type
    FROM enrollments e 
    JOIN users u ON e.user_id = u.user_id 
    JOIN subjects s ON e.subject_id = s.subject_id 
    LEFT JOIN users t ON s.teacher_id = t.id 
    LEFT JOIN student_status st ON u.user_id = st.user_id
    ORDER BY student, s.subject_name
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollments | OSAS Portal</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        form.glass-card select { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: #fff; }
        form.glass-card select option, form.glass-card select optgroup { color: #000; background: #fff; }
        form.glass-card button { width: 100%; background: #39ff14; color: #000; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer; border: none; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
        .regular { background: rgba(0,255,0,0.1); color: #0f0; }
        .irregular { background: rgba(255,165,0,0.1); color: #ffa500; }
        .transferee { background: rgba(0,191,255,0.1); color: #00bfff; }
        .btn-approve { background: #39ff14; color: #000; padding: 6px 15px; border-radius: 4px; border: none; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body class="app-shell">

<?php if ($alert): ?>
<script>alert("<?= htmlspecialchars($alert) ?>");</script>
<?php endif; ?>

<aside class="sidebar">
    <div class="portal-brand">OSAS Portal</div>
    <div class="nav-group">
        <a class="nav-link" href="index.php">Dashboard</a>
        <a class="nav-link" href="students.php">Students</a>
        <a class="nav-link" href="teachers.php">Teachers</a>
        <a class="nav-link" href="subjects.php">Subjects</a>
        <a class="nav-link active" href="enrollments.php">Enrollments</a>
        <a class="nav-link" href="grades.php">Grades</a>
    </div>
    <button class="sign-out-btn" onclick="location.href='/assets/logout.php'">Sign Out</button>
</aside>

<main class="main-view">
    <div class="container">
        <h1>Enrollment Management</h1>

        <div class="glass-card" style="margin-bottom:30px;">
            <h3>Teacher Requests (Irregular/Transferee)</h3>
            <table class="modern-table">
                <thead>
                    <tr><th>Student</th><th>Subject</th><th>Teacher</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($pending_requests)): ?>
                        <tr><td colspan="4" style="text-align:center; padding: 20px;">No pending requests found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pending_requests as $req): ?>
                        <tr>
                            <td><?= htmlspecialchars($req['s_name']) ?></td>
                            <td><?= htmlspecialchars($req['sub_name']) ?></td>
                            <td><?= htmlspecialchars($req['t_name']) ?></td>
                            <td>
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="action" value="approve_request">
                                    <input type="hidden" name="pending_id" value="<?= $req['id'] ?>">
                                    <input type="hidden" name="student_id" value="<?= $req['student_db_id'] ?>">
                                    <input type="hidden" name="subject_id" value="<?= $req['subject_id'] ?>">
                                    <button type="submit" class="btn-approve">Approve</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <form method="POST" class="glass-card">
            <h3>Manual Enrollment (Regular Students)</h3>
            <select name="student_id" required>
                <option value="" disabled selected>Select Regular Student</option>
                <?php foreach ($students as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['fullname']) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="subject_id" required>
                <option value="" disabled selected>Select Subject</option>
                <?php foreach ($subjects as $teacher => $list): ?>
                    <optgroup label="Teacher: <?= htmlspecialchars($teacher) ?>">
                        <?php foreach ($list as $sub): ?>
                            <option value="<?= $sub['subject_id'] ?>"><?= htmlspecialchars($sub['subject_name']) ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
            <button type="submit">Enroll Student</button>
        </form>

        <div class="glass-card" style="margin-top:30px;">
            <h3>Master Enrollment List</h3>
            <table class="modern-table">
                <thead>
                    <tr><th>Student</th><th>Status</th><th>Subject</th><th>Teacher</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($enrollments as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['student']) ?></td>
                        <td><span class="status-badge <?= strtolower($e['student_type']) ?>"><?= htmlspecialchars($e['student_type']) ?></span></td>
                        <td><?= htmlspecialchars($e['subject_name']) ?></td>
                        <td><?= htmlspecialchars($e['teacher']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>
<?php $conn->close(); ?>