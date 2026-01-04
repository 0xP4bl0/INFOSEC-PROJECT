<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>My Courses</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="app-shell">
    <nav class="sidebar">
        <div class="portal-brand">STUDENT PORTAL</div>
        <div class="nav-group"><a href="dashboard.php" class="nav-link">Dashboard</a><a href="courses.php" class="nav-link active">My Courses</a><a href="grades.php" class="nav-link">Grades</a><a href="student_form.php" class="nav-link">Profile</a></div>
        <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
    </nav>
    <main class="main-view">
        <div class="container">
            <h1>My Enrolled Courses</h1>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; margin-top: 30px;">
                <div class="glass-card" style="border-left: 4px solid var(--neon-green);">
                    <span style="color: var(--neon-green); font-weight: 700;">ITEC 101</span>
                    <h3 style="margin: 10px 0;">Object Oriented Programming</h3>
                    <p style="color: #666; font-size: 14px;">3.0 Units • Lecture & Laboratory</p>
                </div>
                <div class="glass-card" style="border-left: 4px solid var(--neon-green);">
                    <span style="color: var(--neon-green); font-weight: 700;">DCIT 22A</span>
                    <h3 style="margin: 10px 0;">Computer Programming 1</h3>
                    <p style="color: #666; font-size: 14px;">3.0 Units • Lecture & Laboratory</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>