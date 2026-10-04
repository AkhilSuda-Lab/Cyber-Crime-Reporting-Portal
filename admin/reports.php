<?php

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once "../config/database.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}


/* Total complaints */

$totalComplaints = 0;

$result = $conn->query(
    "SELECT COUNT(*) AS total FROM complaints"
);

if ($result) {

    $row = $result->fetch_assoc();

    $totalComplaints = $row["total"];

}


/* Complaint status */

$statusResult = $conn->query(
    "SELECT status, COUNT(*) AS total
     FROM complaints
     GROUP BY status
     ORDER BY total DESC"
);

if (!$statusResult) {
    die("Database Error: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Reports - Cyber Crime Portal</title>

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

    max-width: 1100px;

    margin: 40px auto;

    padding: 20px;

}

.back {

    display: inline-block;

    margin-bottom: 20px;

    color: #2563eb;

    text-decoration: none;

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

.summary {

    background: white;

    padding: 25px;

    border-radius: 15px;

    margin-bottom: 25px;

    box-shadow: 0 5px 20px rgba(0,0,0,0.08);

}

.summary h2 {

    margin-bottom: 10px;

}

.total {

    font-size: 35px;

    font-weight: bold;

    color: #2563eb;

}

.table-card {

    background: white;

    padding: 25px;

    border-radius: 15px;

    box-shadow: 0 5px 20px rgba(0,0,0,0.08);

    overflow-x: auto;

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

.status {

    padding: 6px 12px;

    border-radius: 20px;

    background: #dbeafe;

    color: #1d4ed8;

    font-size: 13px;

}

</style>

</head>

<body>

<div class="container">

<a href="dashboard.php" class="back">
← Back to Dashboard
</a>

<div class="header">

<h1> Reports</h1>

<p>
Complaint statistics and reports.
</p>

</div>


<div class="summary">

<h2>Total Complaints</h2>

<div class="total">

<?php echo $totalComplaints; ?>

</div>

</div>


<div class="table-card">

<h2 style="margin-bottom:20px;">
Complaint Status Report
</h2>

<table>

<thead>

<tr>

<th>Status</th>

<th>Total Complaints</th>

</tr>

</thead>

<tbody>

<?php if ($statusResult->num_rows > 0): ?>

<?php while ($row = $statusResult->fetch_assoc()): ?>

<tr>

<td>

<span class="status">

<?php
echo htmlspecialchars(
    $row["status"] ?: "Unknown"
);
?>

</span>

</td>

<td>

<?php
echo htmlspecialchars(
    $row["total"]
);
?>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="2">

No complaint data available.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</body>

</html>