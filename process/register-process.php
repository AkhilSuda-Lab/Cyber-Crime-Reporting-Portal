<?php

session_start();
require_once "../config/database.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../register.php");
    exit;

}
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$state = trim($_POST['state'] ?? '');

if ($full_name === '') {
    $_SESSION['register_error'] ="Please enter your full name.";
    header("Location: ../register.php");
    exit;

}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] ="Please enter a valid email address.";
    header("Location: ../register.php");
    exit;

}

if (!preg_match('/^[0-9]{10}$/', $mobile)) {
    $_SESSION['register_error'] ="Mobile number must contain exactly 10 digits.";
    header("Location: ../register.php");
    exit;

}

if (strlen($password) < 6) {

    $_SESSION['register_error'] ="Password must contain at least 6 characters.";
    header("Location: ../register.php");
    exit;

}

if ($password !== $confirm_password) {

    $_SESSION['register_error'] ="Passwords do not match.";
    header("Location: ../register.php");
    exit;

}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $stmt->close();
    $_SESSION['register_error'] ="An account with this email already exists.";
    header("Location: ../register.php");
    exit;

}

$stmt->close();
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare(
    "INSERT INTO users
    (full_name, email, mobile, password, address, city, state)
    VALUES (?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "sssssss",
    $full_name,
    $email,
    $mobile,
    $hashed_password,
    $address,
    $city,
    $state
);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: ../login.php?registered=1");
    exit;

}

$stmt->close();
$conn->close();
$_SESSION['register_error'] ="Registration failed. Please try again.";
header("Location: ../register.php");
exit;

?>