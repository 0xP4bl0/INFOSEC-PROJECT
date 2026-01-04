<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Profile</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="app-shell">
    <nav class="sidebar">
        <div class="portal-brand">STUDENT PORTAL</div>
        <div class="nav-group"><a href="dashboard.php" class="nav-link">Dashboard</a><a href="courses.php" class="nav-link">My Courses</a><a href="grades.php" class="nav-link">Grades</a><a href="student_form.php" class="nav-link active">Profile</a></div>
        <button class="sign-out-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
    </nav>
    <main class="main-view">
        <div class="container">
            <h1>Student Profile</h1>
            <div class="glass-card">
                <h2 style="margin: 0; font-size: 28px;">JAN DAVE SALAMAT</h2>
                <p style="color: var(--neon-green); font-weight: 700;">ID: 202303600</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px; border-top: 1px solid #333; padding-top: 30px;">
                    <div><p style="color: #666; font-size: 12px;">COURSE</p><p style="font-size: 18px; font-weight: 600;">BS Information Technology</p></div>
                    <div><p style="color: #666; font-size: 12px;">EMAIL</p><p style="font-size: 18px; font-weight: 600;">jan.salamat@cvsu.edu.ph</p></div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>