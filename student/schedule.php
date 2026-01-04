<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: /portal-access.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT day, start_time, end_time, course_code FROM schedule WHERE student_id = ? ORDER BY start_time");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$schedule = [
    'Monday' => [], 'Tuesday' => [], 'Wednesday' => [], 'Thursday' => [], 'Friday' => []
];

while ($row = $result->fetch_assoc()) {
    $schedule[$row['day']][] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Class Schedule | Student Portal</title>
<link rel="stylesheet" href="/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body class="app-shell">
<nav class="sidebar">
    <div class="portal-logo">STUDENT PORTAL</div>
    <a href="dashboard.php" class="nav-link">Dashboard</a>
    <a href="courses.php" class="nav-link">My Courses</a>
    <a href="grades.php" class="nav-link">Grades</a>
    <a href="student_form.php" class="nav-link">Student Profile</a>
    <a href="schedule.php" class="nav-link active">Class Schedule</a>
    <button class="logout-btn" onclick="window.location.href='/assets/logout.php'">Sign Out</button>
</nav>

<main class="main-view">
<div class="content-area">
<header style="margin-bottom: 50px;">
    <h1 style="font-size: 48px; margin: 0; letter-spacing: -2px;">Weekly Timetable</h1>
    <p style="color: #666; font-size: 18px;">Real-time class tracking and schedules</p>
</header>

<div class="glass-card">
<table>
    <thead style="background: rgba(0, 255, 136, 0.05);">
        <tr>
            <th class="time-col">TIME</th>
            <th>MONDAY</th>
            <th>TUESDAY</th>
            <th>WEDNESDAY</th>
            <th>THURSDAY</th>
            <th>FRIDAY</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $timeslots = [
            '07:00 - 10:00', '10:00 - 13:00', '13:00 - 16:00'
        ];
        foreach ($timeslots as $slot) {
            echo "<tr>";
            echo "<td style='text-align:left;font-weight:700;'>$slot</td>";
            foreach (['Monday','Tuesday','Wednesday','Thursday','Friday'] as $day) {
                $cell = "-";
                foreach ($schedule[$day] as $s) {
                    if ("{$s['start_time']} - {$s['end_time']}" === $slot) {
                        $cell = "<div class='schedule-block'>{$s['course_code']}</div>";
                        break;
                    }
                }
                echo "<td>$cell</td>";
            }
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
</div>
</div>
</main>
</body>
</html>
