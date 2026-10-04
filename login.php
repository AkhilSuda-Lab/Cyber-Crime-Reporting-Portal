<?php

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

$registered = isset($_GET['registered']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - Cyber Crime Reporting Portal</title>

    <link rel="stylesheet" href="assets/css/auth.css">

    <style>

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
            font-size: 15px;
            font-weight: normal;
        }

    </style>

</head>

<body>

<div class="auth-page">

    <div class="auth-card">

        <div class="auth-header">

            <h1>Welcome Back</h1>

            <p>
                Login to your Cyber Crime Reporting Portal account.
            </p>

        </div>


        <?php if ($registered): ?>

            <div class="success-message">

                Registration successful.
                You can now login.

            </div>

        <?php endif; ?>


        <?php if ($error): ?>

            <div class="error-message">

                <?php echo htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <form
            action="process/login-process.php"
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
                    placeholder="Enter your email"
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
                    placeholder="Enter your password"
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
                Login
            </button>

        </form>


        <!-- Register -->

        <div class="auth-footer">

            <p>

                Don't have an account?

                <a href="register.php">
                    Register here
                </a>

            </p>

        </div>

    </div>

</div>

</body>

</html>