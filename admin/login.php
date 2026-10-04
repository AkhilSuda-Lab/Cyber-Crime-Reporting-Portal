<?php

session_start();

if (isset($_SESSION["admin_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = $_SESSION["admin_login_error"] ?? "";

unset($_SESSION["admin_login_error"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Login - Cyber Crime Reporting Portal</title>

<link rel="stylesheet" href="../assets/css/auth.css">

<style>

/* Show Password */

.show-password {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-top: 8px;
}

.show-password input[type="checkbox"] {
    width: 16px;
    height: 16px;
    margin: 0;
    padding: 0;
    cursor: pointer;
}

.show-password label {
    margin: 0;
    padding: 0;
    cursor: pointer;
    font-size: 14px;
    font-weight: normal;
    color: #334155;
}

</style>

</head>

<body>

<div class="auth-page">

<div class="auth-card">

<div class="auth-header">

<h1>Admin Login</h1>

<p>
Login to the administration panel.
</p>

</div>


<?php if ($error): ?>

<div class="error-message">

<?php echo htmlspecialchars($error); ?>

</div>

<?php endif; ?>


<form
    action="../process/admin-login-process.php"
    method="POST"
>


<!-- Username -->

<div class="form-group">

<label for="username">
Username
</label>

<input
    type="text"
    id="username"
    name="username"
    placeholder="Enter admin username"
    required
>

</div>


<!-- Password -->

<div class="form-group">

<label for="password">
Password
</label>

<input
    type="password"
    id="password"
    name="password"
    placeholder="Enter admin password"
    required
>


<!-- Show Password -->

<div class="show-password">

<input
    type="checkbox"
    id="showPassword"
    onchange="
        document.getElementById('password').type =
        this.checked ? 'text' : 'password';
    "
>

<label for="showPassword">
Show Password
</label>

</div>

</div>


<!-- Login Button -->

<button
    type="submit"
    class="auth-btn"
>
Admin Login
</button>

</form>

</div>

</div>

</body>

</html>