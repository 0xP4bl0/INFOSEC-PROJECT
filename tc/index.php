<?php
include_once '../assets/auth/auth.php';
include '../config/db.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

<div class="app-shell">

    <aside class="sidebar">
        <div class="portal-brand">Teacher Portal</div>

        <div class="nav-group">
            <a class="nav-link active" href="index.php">Dashboard</a>
            <a class="nav-link" href="my_class.php">My Classes</a>
            <a class="nav-link" href="subjects.php">Manage Subjects</a> 
            <a class="nav-link" href="enrollment_requests.php">Enrollment Requests</a>
            <a class="nav-link" href="students.php">Students</a>
            <a class="nav-link" href="grades.php">Grades</a>
        </div>

        <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
    </aside>

    <main class="main-view">
        <div class="container">

            <h1>Welcome, Teacher 👩‍🏫</h1>
            <p class="subtext">
                Manage your classes, students, and grades
            </p>

            <div class="glass-card">
                <h3>Teacher Information</h3>
                <?php
                $teacher_id = $_SESSION['uid'];
                $query = "SELECT u.fullname, u.email, u.user_id FROM users u WHERE u.user_id = '$teacher_id'";
                $result = mysqli_query($conn, $query);
                
                if($row = mysqli_fetch_array($result)) {
                    echo "<p><strong>Name:</strong> " . htmlspecialchars($row['fullname']) . "</p>";
                    echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
                    echo "<p><strong>Teacher ID:</strong> " . htmlspecialchars($row['user_id']) . "</p>";
                }
                ?>
            </div>

        </div>
    </main>

</div>

</body>
</html>
<?php mysqli_close($conn); ?>