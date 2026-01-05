<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit();
}

$student_id = $_GET['student_id'] ?? null;
$teacher_id = $_SESSION['user_id'];

if (!$student_id) die("Invalid student ID.");

$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'];
    $grade = $_POST['grade'];

    $grade_status = ($grade <= 3.0) ? 'Passed' : 'Failed';

    $check = $conn->prepare("SELECT status FROM grades WHERE student_id = ? AND subject_id = ? AND status = 'Pending'");
    $check->bind_param("si", $student_id, $subject_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $alert = "A release request for this grade is already pending OSAS approval.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO grades (student_id, subject_id, teacher_id, grade, grade_status, status)
            VALUES (?, ?, ?, ?, ?, 'Pending')
            ON DUPLICATE KEY UPDATE grade = VALUES(grade), grade_status = VALUES(grade_status), status = 'Pending'
        ");
        $stmt->bind_param("siids", $student_id, $subject_id, $teacher_id, $grade, $grade_status);
        
        if ($stmt->execute()) {
            $alert = "Grade submitted ($grade_status). Release request sent to OSAS.";
        } else {
            $alert = "Failed to submit release request: " . $conn->error;
        }
        $stmt->close();
    }
    $check->close();
}

$stmt = $conn->prepare("
    SELECT s.subject_id, s.subject_name
    FROM subjects s
    INNER JOIN enrollments e ON s.subject_id = e.subject_id
    WHERE e.user_id = ? AND s.teacher_id = ?
");
$stmt->bind_param("si", $student_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$subjects = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Release Request</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .modern-select, .grade-input {
            width: 100%; 
            margin-top: 8px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .modern-select:focus, .grade-input:focus {
            border-color: var(--neon-green);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 10px rgba(57, 255, 20, 0.2);
        }

        .modern-select option {
            background-color: #1a1a1a;
            color: #fff;
        }

        label {
            display: block;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 15px;
        }
    </style>
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
            <h1>Grade Student</h1>
            <p class="subtext">Submit a grade (1.0 - 5.0). OSAS will approve the release.</p>

            <?php if ($alert): ?>
                <div class="glass-card" style="border-left: 4px solid var(--neon-green); margin-bottom: 20px;">
                    <p style="margin: 0; color: #fff;"><?= htmlspecialchars($alert) ?></p>
                </div>
            <?php endif; ?>

            <div class="glass-card">
                <form method="POST">
                    <div>
                        <label>Select Subject</label>
                        <select name="subject_id" required class="modern-select">
                            <option value="" disabled selected>Choose a subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['subject_id'] ?>"><?= htmlspecialchars($subject['subject_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>Grade (1.00 - 5.00)</label>
                        <input type="number" name="grade" min="1.0" max="5.0" step="0.25" placeholder="e.g. 1.25" required class="grade-input">
                        <small style="color: rgba(255,255,255,0.5); display: block; margin-top: 5px;">
                            <strong>Note:</strong> 1.0 - 3.0 = <span style="color:var(--neon-green)">Passed</span> | 5.0 = <span style="color:#ff5555">Failed</span>
                        </small>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 30px;">
                        <button type="submit" class="neon-btn" style="flex: 1;">Request Release</button>
                        <a href="my_class.php" class="neon-btn" style="flex: 1; text-align: center; background: rgba(255,255,255,0.1); border-color: transparent;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>