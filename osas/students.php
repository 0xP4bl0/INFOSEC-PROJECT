<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'osas') {
    header("Location: /index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ? AND role = 'student'");
    $stmt->bind_param("ss", $status, $user_id);
    $stmt->execute();
    $stmt->close();
}

$students = $conn->query("
    SELECT u.user_id, u.fullname, u.gender, u.email, 
           COALESCE(u.status, 'Inactive') AS account_status,
           COALESCE(ss.status, 'Regular') AS student_type
    FROM users u
    LEFT JOIN student_status ss ON u.user_id = ss.user_id
    WHERE u.role = 'student'
    ORDER BY u.fullname
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students | OSAS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .status-active { color: var(--neon-green); font-weight: 700; }
        .status-inactive { color: #ff5555; font-weight: 700; }
        .action-btn { padding: 8px 14px; border-radius: 8px; font-weight: 700; border: none; cursor: pointer; transition: 0.3s; }
        .block-btn { background: rgba(255, 68, 68, 0.2); color: #ff4444; border: 1px solid #ff4444; }
        .block-btn:hover { background: #ff4444; color: #fff; }
        .activate-btn { background: rgba(57, 255, 20, 0.2); color: var(--neon-green); border: 1px solid var(--neon-green); }
        .activate-btn:hover { background: var(--neon-green); color: #000; }
        
        .type-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: bold;
            background: rgba(255,255,255,0.1);
        }
        .type-regular { color: #00d4ff; border: 1px solid #00d4ff; }
        .type-irregular { color: #ffa500; border: 1px solid #ffa500; }
        .type-transferee { color: #ff00ff; border: 1px solid #ff00ff; }
    </style>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="portal-brand">OSAS Portal</div>
        <div class="nav-group">
            <a class="nav-link" href="index.php">Dashboard</a>
            <a class="nav-link active" href="students.php">Students</a>
            <a class="nav-link" href="teachers.php">Teachers</a>
            <a class="nav-link" href="subjects.php">Subjects</a>
            <a class="nav-link" href="enrollments.php">Enrollments</a>
            <a class="nav-link" href="grades.php">Grades</a>
        </div>
        <button class="sign-out-btn" onclick="location.href='/assets/logout.php'">Sign Out</button>
    </aside>

    <main class="main-view">
        <div class="container">
            <h1>Manage Students</h1>
            <p class="subtext">Monitor student types and account accessibility</p>

            <div class="glass-card">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Account</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($students as $i => $s): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td style="font-family: monospace;"><?= htmlspecialchars($s['user_id']) ?></td>
                            <td><?= htmlspecialchars($s['fullname']) ?></td>
                            <td>
                                <span class="type-badge type-<?= strtolower($s['student_type']) ?>">
                                    <?= htmlspecialchars($s['student_type']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($s['email']) ?></td>
                            <td class="<?= $s['account_status'] === 'Active' ? 'status-active' : 'status-inactive' ?>">
                                <?= htmlspecialchars($s['account_status']) ?>
                            </td>
                            <td>
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="user_id" value="<?= $s['user_id'] ?>">
                                    <?php if ($s['account_status'] === 'Active'): ?>
                                        <input type="hidden" name="status" value="Inactive">
                                        <button class="action-btn block-btn">Block</button>
                                    <?php else: ?>
                                        <input type="hidden" name="status" value="Active">
                                        <button class="action-btn activate-btn">Activate</button>
                                    <?php endif; ?>
                                </form>
                            </td>
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