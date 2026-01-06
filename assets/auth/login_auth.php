<?php
session_start();
include '../../config/db.php';

function detect_auth_bypass($data) {
    if (preg_match("/[';]|--|UNION|SELECT|DROP|OR\s+\d=\d/i", $data)) {
        die("AUTH BYPASS DETECTED");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['user_id'] ?? '');
    $password = trim($_POST['password'] ?? '');

    detect_auth_bypass($user_id);
    detect_auth_bypass($password);

    if ($user_id !== '' && $password !== '') {

        $passLen = strlen($password);
        if ($passLen < 8 || $passLen > 20 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            header("Location: /login/index.php?error=Password must be 8-20 characters and contain both letters and numbers");
            exit();
        }

        $stmt = $conn->prepare("
            SELECT id, password, role, COALESCE(status, 'Inactive') AS status
            FROM users
            WHERE user_id = ?
        ");

        if (!$stmt) {
            die(htmlspecialchars($conn->error));
        }

        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hashed_password, $role, $status);
            $stmt->fetch();

            if (!password_verify($password, $hashed_password)) {
                header("Location: /login/index.php?error=Invalid+password");
                exit();
            }

            if (($role === 'student' || $role === 'teacher') && $status !== 'Active') {
                header("Location: /login/index.php?error=Your+account+is+inactive.+Please+contact+OSAS+for+activation");
                exit();
            }

            $_SESSION['user_id'] = $id;
            $_SESSION['uid'] = $user_id;
            $_SESSION['role'] = $role;

            if ($role === 'teacher') {
                header("Location: /tc/index.php");
            } elseif ($role === 'student') {
                header("Location: /student/index.php");
            } elseif ($role === 'osas') {
                header("Location: /osas/index.php");
            } else {
                header("Location: /login/index.php?error=Invalid+role");
            }
            exit();

        } else {
            header("Location: /login/index.php?error=User+not+found");
            exit();
        }

        $stmt->close();
    } else {
        header("Location: /login/index.php?error=Please+fill+all+fields");
        exit();
    }
} else {
    header("Location: /login/index.php");
    exit();
}
$conn->close();
?>