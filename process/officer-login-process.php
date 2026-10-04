<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../officer/login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    $_SESSION["officer_login_error"] = "Please enter email and password.";
    header("Location: ../officer/login.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, name, email, password, department, status
     FROM officers
     WHERE email = ?
     LIMIT 1"
);

if (!$stmt) {
    $_SESSION["officer_login_error"] = "Database error.";
    header("Location: ../officer/login.php");
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();

    $_SESSION["officer_login_error"] = "Invalid email or password.";
    header("Location: ../officer/login.php");
    exit;
}

$officer = $result->fetch_assoc();

if ($officer["status"] !== "Active") {
    $stmt->close();
    $conn->close();

    $_SESSION["officer_login_error"] = "Your officer account is inactive.";
    header("Location: ../officer/login.php");
    exit;
}

if (!password_verify($password, $officer["password"])) {
    $stmt->close();
    $conn->close();

    $_SESSION["officer_login_error"] = "Invalid email or password.";
    header("Location: ../officer/login.php");
    exit;
}

session_regenerate_id(true);

$_SESSION["officer_id"] = $officer["id"];
$_SESSION["officer_name"] = $officer["name"];
$_SESSION["officer_email"] = $officer["email"];
$_SESSION["officer_department"] = $officer["department"];

$stmt->close();
$conn->close();

header("Location: ../officer/dashboard.php");
exit;

?>