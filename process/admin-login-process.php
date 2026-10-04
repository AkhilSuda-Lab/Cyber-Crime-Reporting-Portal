<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin/login.php");
    exit;
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $password === "") {
    $_SESSION["admin_login_error"] = "Please enter username and password.";
    header("Location: ../admin/login.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, name, username, email, password, status
     FROM admins
     WHERE username = ?
     LIMIT 1"
);

if (!$stmt) {
    $_SESSION["admin_login_error"] = "Database error.";
    header("Location: ../admin/login.php");
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();

    $_SESSION["admin_login_error"] = "Invalid username or password.";
    header("Location: ../admin/login.php");
    exit;
}

$admin = $result->fetch_assoc();

if ($admin["status"] !== "Active") {
    $stmt->close();
    $conn->close();

    $_SESSION["admin_login_error"] = "Admin account is inactive.";
    header("Location: ../admin/login.php");
    exit;
}

if (!password_verify($password, $admin["password"])) {
    $stmt->close();
    $conn->close();

    $_SESSION["admin_login_error"] = "Invalid username or password.";
    header("Location: ../admin/login.php");
    exit;
}

session_regenerate_id(true);

$_SESSION["admin_id"] = $admin["id"];
$_SESSION["admin_name"] = $admin["name"];
$_SESSION["admin_username"] = $admin["username"];
$_SESSION["admin_email"] = $admin["email"];

$stmt->close();
$conn->close();

header("Location: ../admin/dashboard.php");
exit;

?>