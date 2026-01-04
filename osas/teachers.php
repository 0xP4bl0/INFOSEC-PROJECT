<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'osas') {
    header("Location: /index.php");
    exit();
}

$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $target_id = $_POST['user_id'];
    $new_status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ? AND role = 'teacher'");
    $stmt->bind_param("si", $new_status, $target_id);
    if ($stmt->execute()) {
        $alert = "Teacher status updated successfully.";
    }
    $stmt->close();
}

$teachers = $conn->query("
    SELECT user_id, fullname, email, status, gender 
    FROM users 
    WHERE role = 'teacher' 
    ORDER BY fullname ASC
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Teachers | OSAS Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .teacher-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: rgba(57, 255, 20, 0.1);
            color: var(--neon-green);
            border: 1px solid var(--neon-green);
        }

        .status-inactive {
            background: rgba(255, 49, 49, 0.1);
            color: #ff3131;
            border: 1px solid #ff3131;
        }

        .manage-btn {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            transition: 0.3s;
        }

        .manage-btn:hover {
            background: var(--neon-green);
            color: #000;
            border-color: var(--neon-green);
        }

        .modern-table select {
            background: #222;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.1);
            padding: 5px;
            border-radius: 4px;
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
            <a class="nav-link active" href="teachers.php">Teachers</a>
            <a class="nav-link" href="subjects.php">Subjects</a>
            <a class="nav-link" href="enrollments.php">Enrollments</a>
            <a class="nav-link" href="reports.php">Reports</a>
        </div>
        <button class="sign-out-btn" onclick="location.href='/logout.php'">Sign Out</button>
    </aside>

    <main class="main-view">
        <div class="container">
            <h1>Manage Teachers</h1>
            <p class="subtext">Review profiles and manage account accessibility</p>

            <div class="glass-card" style="margin-top: 30px;">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $t): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($t['fullname']) ?></strong></td>
                            <td><?= htmlspecialchars($t['email']) ?></td>
                            <td><?= htmlspecialchars($t['gender']) ?></td>
                            <td>
                                <span class="status-badge <?= $t['status'] === 'Active' ? 'status-active' : 'status-inactive' ?>">
                                    <?= htmlspecialchars($t['status']) ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $t['user_id'] ?>">
                                    <input type="hidden" name="update_status" value="1">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="Active" <?= $t['status'] === 'Active' ? 'selected' : '' ?>>Activate</option>
                                        <option value="Inactive" <?= $t['status'] === 'Inactive' ? 'selected' : '' ?>>Block</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($teachers)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 30px;">No teacher records found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>