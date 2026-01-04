<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register Account</title>
<link rel="stylesheet" href="/css/style.css">

<script>
function toggleFields(select) {
    const dept = document.getElementById('department-field');
    const student = document.getElementById('student-status-field');

    dept.style.display = 'none';
    student.style.display = 'none';

    if (select.value === 'teacher') {
        dept.style.display = 'block';
        dept.querySelector('input').required = true;
        student.querySelector('select').required = false;
    }

    if (select.value === 'student') {
        student.style.display = 'block';
        student.querySelector('select').required = true;
        dept.querySelector('input').required = false;
    }
}
</script>
</head>

<body style="display:flex;justify-content:center;align-items:center;height:100vh;background:#0b0e11;">

<div class="glass-card" style="width:420px;text-align:center;">
<h1 style="margin-bottom:30px;">Create Account</h1>

<form method="POST" action="/assets/auth/reg_process.php">

<select name="role" required onchange="toggleFields(this)"
style="width:100%;padding:16px;background:#000;border:1px solid #333;border-radius:12px;color:#fff;margin-bottom:15px;">
<option value="" disabled selected>Select Role</option>
<option value="student">Student</option>
<option value="teacher">Teacher</option>
</select>

<!-- STUDENT STATUS -->
<div id="student-status-field" style="display:none;margin-bottom:15px;">
<select name="student_status"
style="width:100%;padding:16px;background:#000;border:1px solid #333;border-radius:12px;color:#fff;">
<option value="" disabled selected>Student Status</option>
<option value="regular">Regular</option>
<option value="irregular">Irregular</option>
<option value="transferee">Transferee</option>
</select>
</div>

<!-- DEPARTMENT -->
<div id="department-field" style="display:none;margin-bottom:15px;">
<input type="text" name="department" placeholder="Department"
style="width:100%;padding:16px;background:#000;border:1px solid #333;border-radius:12px;color:#fff;">
</div>

<select name="gender" required
style="width:100%;padding:16px;background:#000;border:1px solid #333;border-radius:12px;color:#fff;margin-bottom:15px;">
<option value="" disabled selected>Select Gender</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
<option value="Other">Other</option>
</select>

<input type="email" name="email" placeholder="Email Address" required
style="width:100%;padding:16px;background:#000;border:1px solid #333;border-radius:12px;color:#fff;margin-bottom:15px;">

<input type="text" name="user_id" placeholder="Student / Teacher ID" required
style="width:100%;padding:16px;background:#000;border:1px solid #333;border-radius:12px;color:#fff;margin-bottom:15px;">

<input type="text" name="fullname" placeholder="Full Name" required
style="width:100%;padding:16px;background:#000;border:1px solid #333;border-radius:12px;color:#fff;margin-bottom:15px;">

<input type="password" name="password" placeholder="Password" required
style="width:100%;padding:16px;background:#000;border:1px solid #333;border-radius:12px;color:#fff;margin-bottom:15px;">

<input type="password" name="confirm_password" placeholder="Confirm Password" required
style="width:100%;padding:16px;background:#000;border:1px solid #333;border-radius:12px;color:#fff;margin-bottom:25px;">

<button type="submit" class="neon-btn" style="width:100%;">Register</button>
</form>

</div>
</body>
</html>
