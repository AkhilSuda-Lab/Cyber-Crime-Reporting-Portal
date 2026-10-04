<?php
session_start();

require_once "../config/database.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../admin/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin/users.php");
    exit;
}

$user_id = (int)($_POST["user_id"] ?? 0);
$status = $_POST["status"] ?? "";

if ($user_id <= 0 || !in_array($status, ["Active", "Inactive"], true)) {
    header("Location: ../admin/users.php");
    exit;
}

$stmt = $conn->prepare(
    "UPDATE users SET status = ? WHERE id = ?"
);

$stmt->bind_param("si", $status, $user_id);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: ../admin/users.php");
exit;
?>