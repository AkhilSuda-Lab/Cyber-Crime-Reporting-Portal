<?php
require_once "../includes/admin-auth.php";
requireAdminLogin();
require_once "../config/database.php";

$total_users = 0;
$total_complaints = 0;
$total_officers = 0;
$submitted = 0;
$under_review = 0;
$assigned = 0;
$investigation = 0;
$resolved = 0;
$closed = 0;
$rejected = 0;

$result = $conn->query("SELECT COUNT(*) AS total FROM users");
if ($result) {
    $total_users = $result->fetch_assoc()["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM complaints");
if ($result) {
    $total_complaints = $result->fetch_assoc()["total"];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM officers");
if ($result) {
    $total_officers = $result->fetch_assoc()["total"];
}

$result = $conn->query(
    "SELECT status, COUNT(*) AS total
     FROM complaints
     GROUP BY status"
);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        switch ($row["status"]) {
            case "Submitted":
                $submitted = $row["total"];
                break;
            case "Under Review":
                $under_review = $row["total"];
                break;
            case "Assigned":
                $assigned = $row["total"];
                break;
            case "Investigation":
                $investigation = $row["total"];
                break;
            case "Resolved":
                $resolved = $row["total"];
                break;
            case "Closed":
                $closed = $row["total"];
                break;
            case "Rejected":
                $rejected = $row["total"];
                break;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard | Cyber Crime Reporting Portal</title>

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

/* SIDEBAR */

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    height: 100vh;
    background: #0f172a;
    color: white;
    padding: 25px 18px;
    z-index: 1000;
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
    transition: 0.2s;
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

/* MAIN */

.main {
    margin-left: 250px;
    padding: 30px;
}

/* TOP BAR */

.topbar {
    background: white;
    border-radius: 15px;
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 3px 15px rgba(15, 23, 42, 0.06);
    margin-bottom: 25px;
}

.topbar h1 {
    font-size: 25px;
    color: #0f172a;
}

.topbar p {
    color: #64748b;
    margin-top: 5px;
    font-size: 14px;
}

.admin-profile {
    display: flex;
    align-items: center;
    gap: 12px;
}

.admin-avatar {
    width: 45px;
    height: 45px;
    background: #2563eb;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.admin-info strong {
    display: block;
}

.admin-info span {
    color: #64748b;
    font-size: 12px;
}

/* STAT CARDS */

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 23px;
    box-shadow: 0 3px 15px rgba(15, 23, 42, 0.06);
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(15, 23, 42, 0.10);
}

.stat-card::after {
    content: "";
    position: absolute;
    width: 70px;
    height: 70px;
    right: -20px;
    top: -20px;
    background: #dbeafe;
    border-radius: 50%;
}

.stat-icon {
    width: 45px;
    height: 45px;
    background: #eff6ff;
    color: #2563eb;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 21px;
    margin-bottom: 15px;
}

.stat-card h3 {
    font-size: 14px;
    color: #64748b;
    font-weight: normal;
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #0f172a;
    margin-top: 7px;
}

/* STATUS */

.section-title {
    margin-bottom: 15px;
    font-size: 20px;
}

.status-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-bottom: 30px;
}

.status-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 3px 12px rgba(15, 23, 42, 0.05);
    border-left: 4px solid #2563eb;
}

.status-card h4 {
    color: #64748b;
    font-size: 13px;
    margin-bottom: 10px;
}

.status-number {
    font-size: 25px;
    font-weight: bold;
    color: #0f172a;
}

/* DIFFERENT STATUS COLORS */

.status-card.submitted {
    border-left-color: #2563eb;
}

.status-card.review {
    border-left-color: #f59e0b;
}

.status-card.assigned {
    border-left-color: #8b5cf6;
}

.status-card.investigation {
    border-left-color: #06b6d4;
}

.status-card.resolved {
    border-left-color: #16a34a;
}

.status-card.closed {
    border-left-color: #64748b;
}

.status-card.rejected {
    border-left-color: #dc2626;
}

/* QUICK ACTIONS */

.actions-section {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(15, 23, 42, 0.06);
}

.actions-section h2 {
    margin-bottom: 18px;
    font-size: 20px;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.action-card {
    text-decoration: none;
    color: #1e293b;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 18px;
    transition: 0.2s;
}

.action-card:hover {
    border-color: #2563eb;
    background: #eff6ff;
    transform: translateY(-2px);
}

.action-icon {
    font-size: 23px;
    margin-bottom: 10px;
}

.action-card strong {
    display: block;
    font-size: 14px;
}

.action-card span {
    display: block;
    color: #64748b;
    font-size: 12px;
    margin-top: 5px;
}


@media (max-width: 1000px) {

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .status-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 700px) {

    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
    }

    .main {
        margin-left: 0;
        padding: 15px;
    }

    .topbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .stats-grid,
    .status-grid,
    .actions-grid {
        grid-template-columns: 1fr;
    }
}

</style>

</head>

<body>



<aside class="sidebar">

<div class="logo">

<div class="logo-icon">
🛡️
</div>

<h2>Cyber Crime Portal</h2>

<p>Administration Panel</p>

</div>

<nav class="nav">

<a href="dashboard.php" class="active">
 &nbsp; Dashboard
</a>

<a href="complaints.php">
 &nbsp; Complaints
</a>

<a href="users.php">
 &nbsp; Users
</a>

<a href="officers.php">
 &nbsp; Officers
</a>

<a href="reports.php">
 &nbsp; Reports
</a>

<a href="logout.php" class="logout">
&nbsp; Logout
</a>

</nav>

</aside>


<main class="main">


<div class="topbar">

<div>

<h1>Admin Dashboard</h1>

<p>
Monitor and manage the Cyber Crime Reporting Portal
</p>

</div>

<div class="admin-profile">

<div class="admin-avatar">
<?php
echo strtoupper(
    substr($_SESSION["admin_name"], 0, 1)
);
?>
</div>

<div class="admin-info">

<strong>
<?php
echo htmlspecialchars($_SESSION["admin_name"]);
?>
</strong>

<span>
Administrator
</span>

</div>

</div>

</div>


<!-- MAIN STATISTICS -->

<div class="stats-grid">

<div class="stat-card">



<h3>Total Registered Users</h3>

<div class="stat-number">
<?php echo $total_users; ?>
</div>

</div>


<div class="stat-card">


<h3>Total Complaints</h3>

<div class="stat-number">
<?php echo $total_complaints; ?>
</div>

</div>


<div class="stat-card">



<h3>Total Officers</h3>

<div class="stat-number">
<?php echo $total_officers; ?>
</div>

</div>

</div>



<h2 class="section-title">
Complaint Status Overview
</h2>

<div class="status-grid">

<div class="status-card submitted">
<h4>Submitted</h4>
<div class="status-number">
<?php echo $submitted; ?>
</div>
</div>

<div class="status-card review">
<h4>Under Review</h4>
<div class="status-number">
<?php echo $under_review; ?>
</div>
</div>

<div class="status-card assigned">
<h4>Assigned</h4>
<div class="status-number">
<?php echo $assigned; ?>
</div>
</div>

<div class="status-card investigation">
<h4>Investigation</h4>
<div class="status-number">
<?php echo $investigation; ?>
</div>
</div>

<div class="status-card resolved">
<h4>Resolved</h4>
<div class="status-number">
<?php echo $resolved; ?>
</div>
</div>

<div class="status-card closed">
<h4>Closed</h4>
<div class="status-number">
<?php echo $closed; ?>
</div>
</div>

<div class="status-card rejected">
<h4>Rejected</h4>
<div class="status-number">
<?php echo $rejected; ?>
</div>
</div>

</div>


<!-- QUICK ACTIONS -->

<div class="actions-section">

<h2>
Quick Management
</h2>

<div class="actions-grid">

<a href="complaints.php" class="action-card">



<strong>Manage Complaints</strong>

<span>
Review and process complaints
</span>

</a>


<a href="users.php" class="action-card">



<strong>Manage Users</strong>

<span>
View registered users
</span>

</a>


<a href="officers.php" class="action-card">


<strong>Manage Officers</strong>

<span>
Manage assigned officers
</span>

</a>


<a href="reports.php" class="action-card">



<strong>View Reports</strong>

<span>
Analyze portal activity
</span>

</a>

</div>

</div>

</main>

</body>
</html>