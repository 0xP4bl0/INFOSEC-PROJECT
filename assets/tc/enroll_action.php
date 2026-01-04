<?php
session_start();
include '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $subject_id = intval($_POST['subject_id']);
    $teacher_id = $_SESSION['user_id'];

    $check = mysqli_query($conn, "SELECT * FROM enrollments WHERE user_id = $user_id AND subject_id = $subject_id");
    if (mysqli_num_rows($check) === 0) {
        mysqli_query($conn, "INSERT INTO enrollments (user_id, subject_id) VALUES ($user_id, $subject_id)");
        mysqli_query($conn, "INSERT INTO classes (teacher_id, student_id) VALUES ($teacher_id, $user_id)");
    }

    header("Location: /tc/students.php");
    exit();
}
?>
