<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["officer_id"])) {
    header("Location: login.php");
    exit;
}

$officer_id = (int)$_SESSION["officer_id"];

$sql = "SELECT id, name, email, mobile, department, status, created_at
        FROM officers
        WHERE id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $officer_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    die("Officer profile not found.");
}

$officer = $result->fetch_assoc();

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>My Profile | Officer Panel</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
    color: #1e293b;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.back {
    display: inline-block;
    margin-bottom: 20px;
    color: #2563eb;
    text-decoration: none;
    font-weight: bold;
}

.card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.header {
    text-align: center;
    margin-bottom: 30px;
}

.profile-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 15px;
    background: #2563eb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
}

.header h1 {
    color: #0f172a;
    margin-bottom: 5px;
}

.header p {
    color: #64748b;
}

.profile-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.profile-item {
    background: #f8fafc;
    padding: 18px;
    border-radius: 10px;
}

.label {
    display: block;
    font-size: 13px;
    color: #64748b;
    margin-bottom: 7px;
}

.value {
    font-size: 16px;
    font-weight: bold;
    color: #0f172a;
    word-break: break-word;
}

.status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    background: #dcfce7;
    color: #166534;
    font-size: 13px;
}

@media (max-width: 700px) {

    .profile-grid {
        grid-template-columns: 1fr;
    }

    .card {
        padding: 20px;
    }

}

</style>

</head>

<body>

<div class="container">

<a href="dashboard.php" class="back">
← Back to Dashboard
</a>

<div class="card">

<div class="header">

<div class="profile-icon">
👮
</div>

<h1>
<?php echo htmlspecialchars($officer["name"]); ?>
</h1>

<p>
Officer Profile
</p>

</div>

<div class="profile-grid">

<div class="profile-item">

<span class="label">
Full Name
</span>

<span class="value">
<?php echo htmlspecialchars($officer["name"]); ?>
</span>

</div>

<div class="profile-item">

<span class="label">
Email Address
</span>

<span class="value">
<?php echo htmlspecialchars($officer["email"]); ?>
</span>

</div>

<div class="profile-item">

<span class="label">
Mobile Number
</span>

<span class="value">
<?php echo htmlspecialchars($officer["mobile"]); ?>
</span>

</div>

<div class="profile-item">

<span class="label">
Department
</span>

<span class="value">
<?php echo htmlspecialchars($officer["department"]); ?>
</span>

</div>

<div class="profile-item">

<span class="label">
Account Status
</span>

<span class="status">
<?php echo htmlspecialchars($officer["status"]); ?>
</span>

</div>

<div class="profile-item">

<span class="label">
Account Created
</span>

<span class="value">
<?php echo htmlspecialchars($officer["created_at"]); ?>
</span>

</div>

</div>

</div>

</div>

</body>

</html>