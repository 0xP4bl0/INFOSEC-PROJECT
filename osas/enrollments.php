<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'osas') {
    header("Location: /index.php");
    exit();
}

$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];

    $statusCheck = $conn->prepare("SELECT status FROM users WHERE user_id = ? AND role = 'student'");
    $statusCheck->bind_param("i", $student_id);
    $statusCheck->execute();
    $statusCheck->bind_result($status);
    $statusCheck->fetch();
    $statusCheck->close();

    if ($status !== 'Active') {
        $alert = "This student account is inactive and cannot be enrolled.";
    } else {
        $check = $conn->prepare("SELECT 1 FROM enrollments WHERE user_id = ? AND subject_id = ?");
        $check->bind_param("ii", $student_id, $subject_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $alert = "Student is already enrolled in this subject.";
        } else {
            $stmt = $conn->prepare("INSERT INTO enrollments (user_id, subject_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $student_id, $subject_id);
            if ($stmt->execute()) {
                $alert = "Student enrolled successfully.";
            } else {
                $alert = "Enrollment failed.";
            }
            $stmt->close();
        }
        $check->close();
    }
}

$students = $conn->query("SELECT user_id, fullname FROM users WHERE role = 'student' AND status = 'Active' ORDER BY fullname")->fetch_all(MYSQLI_ASSOC);

$subjectsRaw = $conn->query("SELECT s.subject_id, s.subject_name, COALESCE(u.fullname, 'Unassigned Teacher') AS teacher FROM subjects s LEFT JOIN users u ON s.teacher_id = u.user_id ORDER BY teacher, s.subject_name")->fetch_all(MYSQLI_ASSOC);

$subjects = [];
foreach ($subjectsRaw as $row) {
    $subjects[$row['teacher']][] = $row;
}

$enrollments = $conn->query("
    SELECT 
        u.fullname AS student, 
        s.subject_name, 
        COALESCE(t.fullname, 'Unassigned') AS teacher 
    FROM enrollments e 
    INNER JOIN users u ON e.user_id = u.user_id 
    INNER JOIN subjects s ON e.subject_id = s.subject_id 
    LEFT JOIN users t ON s.teacher_id = t.id 
    ORDER BY student, s.subject_name
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollments | OSAS Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        form.glass-card select {
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.05);
            color: #fff;
        }
        form.glass-card select option {
            background: #222;
            color: #fff;
        }
        form.glass-card button {
            width: 100%;
            background: var(--neon-green);
            color: #000;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            border: none;
        }
        form.glass-card button:hover {
            box-shadow: 0 0 15px var(--neon-green);
        }
    </style>
</head>
<body>

<?php if ($alert): ?>
<script>alert("<?= htmlspecialchars($alert, ENT_QUOTES) ?>");</script>
<?php endif; ?>

<div class="app-shell">
    <aside class="sidebar">
        <div class="portal-brand">OSAS Portal</div>
        <div class="nav-group">
            <a class="nav-link" href="index.php">Dashboard</a>
            <a class="nav-link" href="students.php">Students</a>
            <a class="nav-link" href="teachers.php">Teachers</a>
            <a class="nav-link" href="subjects.php">Subjects</a>
            <a class="nav-link active" href="enrollments.php">Enrollments</a>
            <a class="nav-link" href="reports.php">Reports</a>
        </div>
        <button class="sign-out-btn" onclick="location.href='/logout.php'">Sign Out</button>
    </aside>

    <main class="main-view">
        <div class="container">
            <h1>Enrollments</h1>
            <p class="subtext">Assign students to subjects</p>

            <form method="POST" class="glass-card" onsubmit="return confirm('Confirm enrollment?')">
                <label>Student</label>
                <select name="student_id" required>
                    <option value="" disabled selected>Select student</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['user_id'] ?>"><?= htmlspecialchars($s['fullname']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Subject</label>
                <select name="subject_id" required>
                    <option value="" disabled selected>Select subject</option>
                    <?php foreach ($subjects as $teacher => $list): ?>
                        <optgroup label="Teacher: <?= htmlspecialchars($teacher) ?>">
                            <?php foreach ($list as $sub): ?>
                                <option value="<?= $sub['subject_id'] ?>">
                                    <?= htmlspecialchars($sub['subject_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Enroll Student</button>
            </form>

            <div class="glass-card" style="margin-top:30px;">
                <h3>Current Enrollments</h3>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrollments as $e): ?>
                        <tr>
                            <td><?= htmlspecialchars($e['student']) ?></td>
                            <td><?= htmlspecialchars($e['subject_name']) ?></td>
                            <td><?= htmlspecialchars($e['teacher']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($enrollments)): ?>
                            <tr><td colspan="3" style="text-align:center;">No enrollments found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>