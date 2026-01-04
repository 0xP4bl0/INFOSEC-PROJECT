<?php
session_start(); 

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'teacher') {
        header("Location: /tc/index.php");
        exit();
    } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
        header("Location: /student/index.php");
        exit();
    }
}

$error = $_GET['error'] ?? '';
if ($error) {
    echo "<script>alert('".htmlspecialchars($error)."');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Portal Access</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body style="display: flex; justify-content: center; align-items: center; height: 100vh; background: #0b0e11;">

<div class="glass-card" style="width: 400px; text-align: center;">
    <h1 style="margin-bottom: 30px; letter-spacing: -1px;">Portal Access</h1>

    <form action="/assets/auth/login_auth.php" method="POST">
        <input type="text" name="user_id" placeholder="User ID"
            style="width: 100%; padding: 16px; background: #000; border: 1px solid #333; border-radius: 12px; color: #fff; margin-bottom: 15px; outline: none;">

        <input type="password" name="password" placeholder="Password"
            style="width: 100%; padding: 16px; background: #000; border: 1px solid #333; border-radius: 12px; color: #fff; margin-bottom: 25px; outline: none;">

        <button type="submit" class="neon-btn" style="width: 100%;">
            Authorize Session
        </button>
    </form>

    <p style="margin-top: 25px; color: #888; font-size: 14px;">
        Don’t have an account?
    </p>

    <button type="button"
        style="margin-top: 10px; width: 100%; padding: 14px; background: transparent; border: 1px solid #444; border-radius: 12px; color: #00ff88; cursor: pointer;"
        onclick="window.location.href='/register'">
        Register
    </button>
</div>

</body>
</html>
