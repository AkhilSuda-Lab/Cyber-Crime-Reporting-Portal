<?php

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit;
}

$error = $_SESSION['register_error'] ?? '';
unset($_SESSION['register_error']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Register - Cyber Crime Reporting Portal</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Create Account</h1>
            <p>Register to report and track cybercrime complaints.</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>


        <form
            action="process/register-process.php"
            method="POST"
            id="registerForm"
        >
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    placeholder="Enter your full name"
                    required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email address"
                    required>
            </div>

            <div class="form-group">
                <label for="mobile">Mobile Number</label>
                <input
                    type="tel"
                    id="mobile"
                    name="mobile"
                    placeholder="Enter 10-digit mobile number"
                    maxlength="10"
                    required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm password"
                    required>
            </div>


            <div class="form-group">
                <label for="address">Address</label>
                <textarea
                    id="address"
                    name="address"
                    placeholder="Enter your address"
                    rows="3">
                </textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="city">City</label>
                    <input
                        type="text"
                        id="city"
                        name="city"
                        placeholder="City">
                </div>


            <div class="form-group">
                <label for="state">State</label>
                    <input
                        type="text"
                        id="state"
                        name="state"
                        placeholder="State">
                </div>
            </div>

            <button type="submit"class="auth-btn">Create Account</button>
        </form>

        <div class="auth-footer">
            <p>Already have an account?
                <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
</div>

<script src="assets/js/validation.js"></script>
</body>
</html>