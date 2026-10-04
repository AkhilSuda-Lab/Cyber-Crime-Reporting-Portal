<?php

session_start();

if (isset($_SESSION["officer_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = $_SESSION["officer_login_error"] ?? "";

unset($_SESSION["officer_login_error"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Officer Login | Cyber Crime Reporting Portal</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    min-height: 100vh;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-container {
    width: 100%;
    max-width: 420px;
    padding: 20px;
}

.login-card {
    background: white;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.12);
}

.logo {
    text-align: center;
    margin-bottom: 25px;
}

.logo-icon {
    width: 65px;
    height: 65px;
    margin: auto;
    background: #2563eb;
    color: white;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
}

.logo h1 {
    margin-top: 15px;
    font-size: 22px;
    color: #0f172a;
}

.logo p {
    margin-top: 6px;
    color: #64748b;
    font-size: 13px;
}

.error {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 18px;
    font-size: 14px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 7px;
    font-size: 14px;
    font-weight: bold;
    color: #334155;
}

.form-group input {
    width: 100%;
    padding: 13px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
}

.form-group input:focus {
    border-color: #2563eb;
}

/* Show Password */

.show-password {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-top: 8px;
}

.show-password input[type="checkbox"] {
    width: 16px !important;
    height: 16px !important;
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

.login-btn {
    width: 100%;
    padding: 13px;
    border: none;
    border-radius: 8px;
    background: #2563eb;
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
}

.login-btn:hover {
    background: #1d4ed8;
}

.back {
    text-align: center;
    margin-top: 20px;
}

.back a {
    color: #2563eb;
    text-decoration: none;
    font-size: 14px;
}

.back a:hover {
    text-decoration: underline;
}

</style>

</head>

<body>

<div class="login-container">

<div class="login-card">

<div class="logo">

<div class="logo-icon">
👮
</div>

<h1>Officer Login</h1>

<p>Cyber Crime Reporting Portal</p>

</div>


<?php if ($error !== ""): ?>

<div class="error">

<?php echo htmlspecialchars($error); ?>

</div>

<?php endif; ?>


<form
    action="../process/officer-login-process.php"
    method="POST"
>


<!-- Email -->

<div class="form-group">

<label for="email">
Email Address
</label>

<input
    type="email"
    id="email"
    name="email"
    placeholder="Enter officer email"
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
    placeholder="Enter password"
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
    class="login-btn"
>
Login
</button>

</form>


<div class="back">

<a href="../index.php">
← Back to Portal
</a>

</div>

</div>

</div>

</body>

</html>