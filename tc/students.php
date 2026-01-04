<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /login");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student List</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        .glass-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }
        .modern-table th, .modern-table td {
            padding: 10px;
            border-bottom: 1px solid #444;
            text-align: left;
        }
        .modern-table th {
            background-color: rgba(255,255,255,0.1);
        }
        select, button {
            padding: 5px 10px;
            margin-right: 5px;
            border-radius: 6px;
            border: none;
        }
        button {
            background-color: var(--neon-green);
            color: #000;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="portal-brand">Teacher Portal</div>
        <div class="nav-group">
            <a class="nav-link" href="index.php">Dashboard</a>
            <a class="nav-link" href="my_class.php">My Classes</a>
            <a class="nav-link" href="subjects.php">Manage Subjects</a>
            <a class="nav-link active" href="students.php">Students</a>
            <a class="nav-link" href="#">Grades</a>
            <a class="nav-link" href="#">Announcements</a>
        </div>
        <button class="sign-out-btn" onclick="location.href='/logout.php'">Sign Out</button>
    </aside>

    <main class="main-view">
        <div class="container">

            <div class="glass-card">
                <h2 style="color: var(--neon-green); margin-bottom: 10px;">Enrolled Students</h2>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Subjects</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $enrolled = mysqli_query($conn, "
                            SELECT u.user_id, u.fullname, u.email, GROUP_CONCAT(s.subject_name SEPARATOR ', ') AS subjects
                            FROM users u
                            INNER JOIN enrollments e ON u.user_id = e.user_id
                            INNER JOIN subjects s ON e.subject_id = s.subject_id
                            WHERE u.role = 'student'
                            GROUP BY u.user_id
                            ORDER BY u.fullname
                        ");

                        if (mysqli_num_rows($enrolled) > 0) {
                            while ($row = mysqli_fetch_assoc($enrolled)) {
                                echo "
                                <tr>
                                    <td>{$row['user_id']}</td>
                                    <td>{$row['fullname']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['subjects']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center;'>No enrolled students</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="glass-card">
                <h2 style="color:#ff5555; margin-bottom: 10px;">Not Enrolled (Irregular Students Only)</h2>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Filter by 'irregular' status from the student_status table
                        $not_enrolled = mysqli_query($conn, "
                            SELECT u.user_id, u.fullname, u.email
                            FROM users u
                            JOIN student_status ss ON u.user_id = ss.user_id
                            WHERE u.role = 'student'
                            AND ss.status = 'irregular'
                            AND u.user_id NOT IN (
                                SELECT user_id FROM enrollments
                            )
                            ORDER BY u.fullname
                        ");

                        if (mysqli_num_rows($not_enrolled) > 0) {
                            while ($row = mysqli_fetch_assoc($not_enrolled)) {
                                echo "
                                <tr>
                                    <td>{$row['user_id']}</td>
                                    <td>{$row['fullname']}</td>
                                    <td>{$row['email']}</td>
                                    <td>
                                        <form method='POST' action='/assets/tc/enroll_action.php' style='display:inline;'>
                                            <input type='hidden' name='user_id' value='{$row['user_id']}'>
                                            <select name='subject_id' required>
                                                <option value='' disabled selected>Select Subject</option>";
                                                $subjects = mysqli_query($conn, "SELECT * FROM subjects ORDER BY subject_name");
                                                while($sub = mysqli_fetch_assoc($subjects)){
                                                    echo "<option value='{$sub['subject_id']}'>{$sub['subject_name']}</option>";
                                                }
                                echo "      </select>
                                            <button type='submit'>Enroll</button>
                                        </form>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center;'>No irregular students currently pending enrollment</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>
</body>
</html>