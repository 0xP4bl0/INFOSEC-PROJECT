<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'osas') {
    header("Location: /index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_POST['user_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ? AND role = 'student'");
    $stmt->bind_param("si", $status, $user_id);
    $stmt->execute();
    $stmt->close();
}

$students = $conn->query("
    SELECT user_id, fullname, gender, email, COALESCE(status, 'Inactive') AS status
    FROM users
    WHERE role = 'student'
    ORDER BY fullname
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Students</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/css/style.css">
<style>
.status-active { color: var(--neon-green); font-weight: 700; }
.status-inactive { color: #ff5555; font-weight: 700; }

.action-btn {
    padding: 10px 16px;
    border-radius: 10px;
    font-weight: 700;
    border: none;
    cursor: pointer;
}

.block-btn { background: #ff4444; color: #fff; }
.activate-btn { background: var(--neon-green); color: #000; }
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
            <a class="nav-link" href="reports.php">Reports</a>
        </div>
        <button class="sign-out-btn" onclick="location.href='/logout.php'">Sign Out</button>
    </aside>

    <main class="main-view">
        <div class="container">
            <h1>Manage Students</h1>
            <p class="subtext">Activate or block student accounts</p>

            <div class="glass-card">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($students as $i => $s): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($s['fullname']) ?></td>
                            <td><?= htmlspecialchars($s['gender']) ?></td>
                            <td><?= htmlspecialchars($s['email']) ?></td>
                            <td class="<?= $s['status'] === 'Active' ? 'status-active' : 'status-inactive' ?>">
                                <?= htmlspecialchars($s['status']) ?>
                            </td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="user_id" value="<?= $s['user_id'] ?>">
                                    <?php if ($s['status'] === 'Active'): ?>
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
