<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'osas') {
    header("Location: /index.php");
    exit();
}

$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    $subject_code = $_POST['subject_code'];
    $subject_name = $_POST['subject_name'];
    $teacher_id = $_POST['teacher_id'];

    $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name, teacher_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $subject_code, $subject_name, $teacher_id);
    
    if ($stmt->execute()) {
        $alert = "Subject created successfully.";
    } else {
        $alert = "Error creating subject.";
    }
    $stmt->close();
}

$teachers = $conn->query("SELECT id, fullname FROM users WHERE role = 'teacher' AND status = 'Active' ORDER BY fullname ASC")->fetch_all(MYSQLI_ASSOC);

$subjects = $conn->query("
    SELECT s.subject_id, s.subject_code, s.subject_name, COALESCE(u.fullname, 'Unassigned') AS teacher_name 
    FROM subjects s 
    LEFT JOIN users u ON s.teacher_id = u.id 
    ORDER BY s.subject_code ASC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Subjects | OSAS Portal</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        form.glass-card input, form.glass-card select {
            width: 100%;
            padding: 14px 16px;
            margin-bottom: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.05);
            color: #fff;
        }
        form.glass-card select option { background: #fff; color: #000; }
        form.glass-card button {
            width: 100%;
            background: #39ff14;
            color: #000;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            border: none;
        }
    </style>
</head>
<body>

<?php if ($alert): ?>
<script>alert("<?= htmlspecialchars($alert) ?>");</script>
<?php endif; ?>

<div class="app-shell">
    <aside class="sidebar">
        <div class="portal-brand">OSAS Portal</div>
        <div class="nav-group">
            <a class="nav-link" href="index.php">Dashboard</a>
            <a class="nav-link" href="students.php">Students</a>
            <a class="nav-link" href="teachers.php">Teachers</a>
            <a class="nav-link" href="subjects.php">Subjects</a>
            <a class="nav-link" href="enrollments.php">Enrollments</a>
            <a class="nav-link" href="grades.php">Grades</a> 
        </div>
        <button class="sign-out-btn" onclick="location.href='/assets/logout.php'">Sign Out</button>
    </aside>

    <main class="main-view">
        <div class="container">
            <h1>Manage Subjects</h1>
            
            <form method="POST" class="glass-card">
                <input type="hidden" name="add_subject" value="1">
                
                <label>Subject Code</label>
                <input type="text" name="subject_code" placeholder="e.g. ITEC 50" required>

                <label>Subject Name</label>
                <input type="text" name="subject_name" placeholder="e.g. Information Security" required>

                <label>Assign Teacher</label>
                <select name="teacher_id" required>
                    <option value="" disabled selected>Select an instructor</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['fullname']) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Create Subject</button>
            </form>

            <div class="glass-card" style="margin-top: 30px;">
                <h3>Subject List</h3>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Subject Name</th>
                            <th>Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subjects as $s): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($s['subject_code']) ?></strong></td>
                            <td><?= htmlspecialchars($s['subject_name']) ?></td>
                            <td><?= htmlspecialchars($s['teacher_name']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>
<?php 
$conn->close();
?>