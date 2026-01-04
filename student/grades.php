<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Grades</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="app-shell">
    <nav class="sidebar">
        <div class="portal-brand">STUDENT PORTAL</div>
        <div class="nav-group"><a href="dashboard.php" class="nav-link">Dashboard</a><a href="courses.php" class="nav-link">My Courses</a><a href="grades.php" class="nav-link active">Grades</a><a href="student_form.php" class="nav-link">Profile</a></div>
        <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
    </nav>
    <main class="main-view">
        <div class="container">
            <h1>Academic Grades</h1>
            <div class="glass-card" style="padding: 0; overflow: hidden;">
                <table class="modern-table">
                    <thead><tr><th>Subject</th><th>Description</th><th style="text-align: center;">Grade</th><th style="text-align: center;">Status</th></tr></thead>
                    <tbody>
                        <tr><td>GNED 11</td><td>ETHICS</td><td style="text-align: center;">1.25</td><td style="text-align: center; color: var(--neon-green);">PASSED</td></tr>
                        <tr><td>DCIT 22A</td><td>COMPUTER PROGRAMMING 1</td><td style="text-align: center;">1.75</td><td style="text-align: center; color: var(--neon-green);">PASSED</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>