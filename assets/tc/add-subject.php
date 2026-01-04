<?php
session_start();
include '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: /index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = trim($_POST['subject_code'] ?? '');
    $subject_name = trim($_POST['subject_name'] ?? '');

    if (empty($subject_code) || empty($subject_name)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name, teacher_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $subject_code, $subject_name, $teacher_id);

        if ($stmt->execute()) {
            $success = "Subject added successfully!";
        } else {
            $error = "Error adding subject: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>