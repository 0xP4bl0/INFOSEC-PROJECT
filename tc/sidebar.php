<aside class="sidebar">
    <div class="portal-brand">Teacher Portal</div>
    <div class="nav-group">
        <a class="nav-link" href="index.php">Dashboard</a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'my_class.php' ? 'active' : '' ?>" href="my_class.php">My Classes</a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'subjects.php' ? 'active' : '' ?>" href="subjects.php">Manage Subjects</a>
        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'students.php' ? 'active' : '' ?>" href="students.php">Students</a>
        <a class="nav-link" href="#">Grades</a>
        <a class="nav-link" href="#">Announcements</a>
    </div>
    <button class="sign-out-btn" onclick="window.location.href='/logout.php'">Sign Out</button>
</aside>
