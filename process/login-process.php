<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    $_SESSION["login_error"] = "Please enter email and password.";
    header("Location: ../login.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, full_name, email, password, status
     FROM users
     WHERE email = ?
     LIMIT 1"
);

if (!$stmt) {
    $_SESSION["login_error"] = "Database error.";
    header("Location: ../login.php");
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();

    $_SESSION["login_error"] = "Invalid email or password.";
    header("Location: ../login.php");
    exit;
}

$user = $result->fetch_assoc();

if ($user["status"] !== "Active") {
    $stmt->close();
    $conn->close();

    $_SESSION["login_error"] = "Your account has been deactivated. Please contact the administrator.";
    header("Location: ../login.php");
    exit;
}

if (!password_verify($password, $user["password"])) {
    $stmt->close();
    $conn->close();

    $_SESSION["login_error"] = "Invalid email or password.";
    header("Location: ../login.php");
    exit;
}

session_regenerate_id(true);

$_SESSION["user_id"] = $user["id"];
$_SESSION["user_name"] = $user["full_name"];
$_SESSION["user_email"] = $user["email"];

$stmt->close();
$conn->close();

header("Location: ../user/dashboard.php");
exit;

?>