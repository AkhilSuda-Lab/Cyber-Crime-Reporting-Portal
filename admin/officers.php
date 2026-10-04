<?php

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once "../config/database.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$result = $conn->query("SELECT * FROM officers ORDER BY id DESC");

if (!$result) {
    die("Database Error: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Officers - Cyber Crime Portal</title>

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
    color: #1e293b;
}

.container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

.header {
    background: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.header h1 {
    color: #0f172a;
    margin-bottom: 8px;
}

.header p {
    color: #64748b;
}

.back {
    display: inline-block;
    margin-bottom: 20px;
    color: #2563eb;
    text-decoration: none;
}

.table-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    overflow-x: auto;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #0f172a;
    color: white;
    padding: 14px;
    text-align: left;
}

td {
    padding: 14px;
    border-bottom: 1px solid #e2e8f0;
}

tr:hover {
    background: #f8fafc;
}

.status {
    padding: 6px 12px;
    border-radius: 20px;
    background: #dcfce7;
    color: #166534;
    font-size: 13px;
}

.empty {
    text-align: center;
    padding: 30px;
    color: #64748b;
}

</style>

</head>

<body>

<div class="container">

<a href="dashboard.php" class="back">
← Back to Dashboard
</a>

<div class="header">

<h1> Manage Officers</h1>

<p>
View and manage registered cyber crime officers.
</p>

</div>

<div class="table-card">

<?php if ($result->num_rows > 0): ?>

<table>

<thead>

<tr>

<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Status</th>

</tr>

</thead>

<tbody>

<?php while ($officer = $result->fetch_assoc()): ?>

<tr>

<td>
<?php echo htmlspecialchars($officer["id"] ?? ""); ?>
</td>

<td>
<?php
echo htmlspecialchars(
    $officer["name"]
    ?? $officer["full_name"]
    ?? "N/A"
);
?>
</td>

<td>
<?php
echo htmlspecialchars(
    $officer["email"]
    ?? "N/A"
);
?>
</td>

<td>

<span class="status">

<?php
echo htmlspecialchars(
    $officer["status"]
    ?? "Active"
);
?>

</span>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

<?php else: ?>

<div class="empty">

No officers found.

</div>

<?php endif; ?>

</div>

</div>

</body>

</html>