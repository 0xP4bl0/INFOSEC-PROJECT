<?php
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /index.php");
    exit();
}

$role = trim($_POST['role'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$email = trim($_POST['email'] ?? '');
$user_id = trim($_POST['user_id'] ?? '');
$fullname = trim($_POST['fullname'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
$department = trim($_POST['department'] ?? '');
$student_status = trim($_POST['student_status'] ?? '');

if (!$role || !$gender || !$email || !$user_id || !$fullname || !$password || !$confirm) {
    echo "<script>alert('All fields are required'); window.history.back();</script>";
    exit();
}

$passLen = strlen($password);
if ($passLen < 8 || $passLen > 20) {
    echo "<script>alert('Password must be between 8 and 20 characters long'); window.history.back();</script>";
    exit();
}

if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
    echo "<script>alert('Password must contain both letters and numbers'); window.history.back();</script>";
    exit();
}

if (!in_array($role, ['student', 'teacher'], true)) {
    echo "<script>alert('Invalid role selected'); window.history.back();</script>";
    exit();
}

if ($role === 'student' && !in_array($student_status, ['regular', 'irregular', 'transferee'], true)) {
    echo "<script>alert('Please select student status'); window.history.back();</script>";
    exit();
}

if (!in_array($gender, ['Male', 'Female', 'Other'], true)) {
    echo "<script>alert('Invalid gender selected'); window.history.back();</script>";
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email address'); window.history.back();</script>";
    exit();
}

if ($password !== $confirm) {
    echo "<script>alert('Passwords do not match'); window.history.back();</script>";
    exit();
}

$check = $conn->prepare("SELECT id FROM users WHERE user_id = ? OR email = ?");
$check->bind_param("ss", $user_id, $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "<script>alert('User ID or Email already exists'); window.history.back();</script>";
    exit();
}
$check->close();

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO users (user_id, fullname, email, role, gender, password, status)
     VALUES (?, ?, ?, ?, ?, ?, 'Inactive')"
);
$stmt->bind_param("ssssss", $user_id, $fullname, $email, $role, $gender, $hashed);

if ($stmt->execute()) {

    if ($role === 'teacher' && $department) {
        $dept_stmt = $conn->prepare(
            "INSERT INTO department (user_id, department) VALUES (?, ?)"
        );
        $dept_stmt->bind_param("ss", $user_id, $department);
        $dept_stmt->execute();
        $dept_stmt->close();
    }

    if ($role === 'student') {
        $status_stmt = $conn->prepare(
            "INSERT INTO student_status (user_id, status) VALUES (?, ?)"
        );
        $status_stmt->bind_param("ss", $user_id, $student_status);
        $status_stmt->execute();
        $status_stmt->close();
    }

    echo "<script>
        alert('Registration successful! Your account is pending OSAS activation.');
        window.location.href='/login';
    </script>";

} else {
    echo "<script>alert('Error during registration'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
exit();
?>