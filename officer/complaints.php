<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["officer_id"])) {
    header("Location: login.php");
    exit;
}

$officerId = (int)$_SESSION["officer_id"];
$complaints = [];

$stmt = $conn->prepare(
    "SELECT
        id,
        complaint_id,
        category,
        incident_date,
        incident_time,
        location,
        amount,
        status,
        created_at,
        updated_at
     FROM complaints
     WHERE assigned_officer_id = ?
     ORDER BY created_at DESC"
);

$stmt->bind_param("i", $officerId);
$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Complaints | Cyber Crime Reporting Portal</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f7fb;
    color: #1e293b;
}

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    height: 100vh;
    background: #0f172a;
    color: white;
    padding: 25px 18px;
}

.logo {
    text-align: center;
    margin-bottom: 35px;
}

.logo-icon {
    width: 55px;
    height: 55px;
    margin: auto;
    background: #2563eb;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
}

.logo h2 {
    margin-top: 12px;
    font-size: 18px;
}

.logo p {
    color: #94a3b8;
    font-size: 12px;
    margin-top: 5px;
}

.nav {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.nav a {
    color: #cbd5e1;
    text-decoration: none;
    padding: 13px 15px;
    border-radius: 9px;
    font-size: 14px;
}

.nav a:hover,
.nav a.active {
    background: #2563eb;
    color: white;
}

.nav a.logout {
    margin-top: 20px;
    background: #dc2626;
    color: white;
}

.main {
    margin-left: 250px;
    padding: 30px;
}

.header {
    margin-bottom: 25px;
}

.header h1 {
    font-size: 28px;
    margin-bottom: 8px;
}

.header p {
    color: #64748b;
}

.card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 3px 15px rgba(15,23,42,.06);
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    min-width: 850px;
}

.table th {
    text-align: left;
    padding: 14px;
    background: #f8fafc;
    color: #475569;
    font-size: 13px;
    border-bottom: 1px solid #e2e8f0;
}

.table td {
    padding: 15px 14px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
}

.table tr:hover {
    background: #f8fafc;
}

.complaint-id {
    color: #2563eb;
    font-weight: bold;
}

.status {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.status-submitted {
    background: #dbeafe;
    color: #1d4ed8;
}

.status-review {
    background: #fef3c7;
    color: #92400e;
}

.status-assigned {
    background: #e0e7ff;
    color: #4338ca;
}

.status-investigation {
    background: #ffedd5;
    color: #c2410c;
}

.status-resolved {
    background: #dcfce7;
    color: #166534;
}

.status-closed {
    background: #e2e8f0;
    color: #475569;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

.view-btn {
    display: inline-block;
    padding: 8px 13px;
    background: #2563eb;
    color: white;
    text-decoration: none;
    border-radius: 7px;
    font-size: 12px;
    font-weight: bold;
}

.view-btn:hover {
    background: #1d4ed8;
}

.empty {
    text-align: center;
    padding: 50px 20px;
    color: #64748b;
}

.empty-icon {
    font-size: 45px;
    margin-bottom: 15px;
}

.empty h3 {
    color: #334155;
    margin-bottom: 8px;
}

@media(max-width:800px) {

    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .main {
        margin-left: 0;
        padding: 15px;
    }

}

</style>

</head>

<body>

<aside class="sidebar">

<div class="logo">

<div class="logo-icon">
👮
</div>

<h2>Cyber Crime Portal</h2>

<p>Officer Panel</p>

</div>

<nav class="nav">

<a href="dashboard.php">
 &nbsp; Dashboard
</a>

<a href="complaints.php" class="active">
 &nbsp; My Complaints
</a>

<a href="profile.php">
 &nbsp; My Profile
</a>

<a href="logout.php" class="logout">
 &nbsp; Logout
</a>

</nav>

</aside>

<main class="main">

<div class="header">

<h1>My Complaints</h1>

<p>
Complaints assigned to you for investigation.
</p>

</div>

<div class="card">

<?php if (count($complaints) > 0): ?>

<table class="table">

<thead>

<tr>

<th>Complaint ID</th>

<th>Category</th>

<th>Incident Date</th>

<th>Location</th>

<th>Amount</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php foreach ($complaints as $complaint): ?>

<?php

$statusClass = "status-submitted";

switch ($complaint["status"]) {

    case "Under Review":
        $statusClass = "status-review";
        break;

    case "Assigned":
        $statusClass = "status-assigned";
        break;

    case "Investigation":
        $statusClass = "status-investigation";
        break;

    case "Resolved":
        $statusClass = "status-resolved";
        break;

    case "Closed":
        $statusClass = "status-closed";
        break;

    case "Rejected":
        $statusClass = "status-rejected";
        break;
}

?>

<tr>

<td>

<span class="complaint-id">

<?php echo htmlspecialchars($complaint["complaint_id"]); ?>

</span>

</td>

<td>

<?php echo htmlspecialchars($complaint["category"]); ?>

</td>

<td>

<?php echo htmlspecialchars($complaint["incident_date"]); ?>

</td>

<td>

<?php echo htmlspecialchars($complaint["location"]); ?>

</td>

<td>

₹<?php echo number_format((float)$complaint["amount"], 2); ?>

</td>

<td>

<span class="status <?php echo $statusClass; ?>">

<?php echo htmlspecialchars($complaint["status"]); ?>

</span>

</td>

<td>

<a
    href="complaint-details.php?id=<?php echo (int)$complaint["id"]; ?>"
    class="view-btn"
>
View
</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<?php else: ?>

<div class="empty">



<h3>No Complaints Assigned</h3>

<p>
There are currently no complaints assigned to you.
</p>

</div>

<?php endif; ?>

</div>

</main>

</body>

</html>