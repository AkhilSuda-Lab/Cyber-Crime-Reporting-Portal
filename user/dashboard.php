<?php

require_once "../includes/auth.php";
requireUserLogin();

require_once "../config/database.php";

$user_id = $_SESSION["user_id"];

$user_name = $_SESSION["user_name"] ?? "User";
$user_email = $_SESSION["user_email"] ?? "";

$totalComplaints = 0;
$pendingComplaints = 0;
$resolvedComplaints = 0;
$closedComplaints = 0;

$sql = "SELECT
            COUNT(*) AS total,
            SUM(status NOT IN ('Resolved', 'Closed', 'Rejected')) AS pending,
            SUM(status = 'Resolved') AS resolved,
            SUM(status = 'Closed') AS closed
        FROM complaints
        WHERE user_id = ?";

$stmt = $conn->prepare($sql);

if ($stmt) {

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();

    $totalComplaints = (int)($stats["total"] ?? 0);
    $pendingComplaints = (int)($stats["pending"] ?? 0);
    $resolvedComplaints = (int)($stats["resolved"] ?? 0);
    $closedComplaints = (int)($stats["closed"] ?? 0);

    $stmt->close();
}

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

<title>User Dashboard | Cyber Crime Reporting Portal</title>

<link
    rel="stylesheet"
    href="../assets/css/dashboard.css"
>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f7fb;
    color: #172033;
}

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 250px;
    height: 100vh;
    background: #12304d;
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
    background: #1976d2;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
}

.logo h2 {
    margin: 12px 0 0;
    font-size: 18px;
}

.logo p {
    color: #b8c7d9;
    font-size: 12px;
    margin-top: 5px;
}

.nav {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.nav a {
    color: #dce6f0;
    text-decoration: none;
    padding: 13px 15px;
    border-radius: 9px;
    font-size: 14px;
}

.nav a:hover,
.nav a.active {
    background: #1976d2;
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

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
}

.welcome h1 {
    margin: 0 0 8px;
    color: #12304d;
}

.welcome p {
    margin: 0;
    color: #64748b;
}

.profile {
    display: flex;
    align-items: center;
    gap: 12px;
    background: white;
    padding: 10px 15px;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
}

.profile-icon {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #1976d2;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.profile-name {
    font-weight: bold;
}

.profile-email {
    font-size: 12px;
    color: #64748b;
    margin-top: 3px;
}

.quick-action {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 25px;
}

.hero,
.security-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
}

.hero h2,
.security-card h3 {
    margin-top: 0;
    color: #12304d;
}

.hero p,
.security-card p {
    color: #64748b;
    line-height: 1.6;
}

.report-btn {
    display: inline-block;
    margin-top: 10px;
    padding: 11px 18px;
    background: #1976d2;
    color: white;
    text-decoration: none;
    border-radius: 7px;
    font-weight: bold;
}

.report-btn:hover {
    background: #125ca1;
}

.security-icon {
    font-size: 30px;
    margin-bottom: 10px;
}

.stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.stat-card {
    background: white;
    padding: 22px;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.06);
}

.stat-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-number {
    font-size: 30px;
    font-weight: bold;
    color: #12304d;
}

.stat-title {
    margin-top: 6px;
    color: #64748b;
}

.stat-icon {
    font-size: 28px;
}

@media (max-width: 1000px) {

    .stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .quick-action {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 800px) {

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
    }

}

@media (max-width: 500px) {

    .stats {
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

<p>User Panel</p>

</div>

<nav class="nav">

<a href="dashboard.php" class="active">
 &nbsp; Dashboard
</a>

<a href="../report-crime.php">
 &nbsp; Report Cyber Crime
</a>

<a href="my-complaints.php">
 &nbsp; My Complaints
</a>

<a href="notifications.php">
 &nbsp; Notifications
</a>

<a href="profile.php">
 &nbsp; My Profile
</a>



<a href="../logout.php" class="logout">
 &nbsp; Logout
</a>

</nav>

</aside>

<main class="main">

<div class="topbar">

<div class="welcome">

<h1>
Welcome back,
<?php echo htmlspecialchars($user_name); ?> 
</h1>

<p>
Manage your cybercrime complaints securely from your dashboard.
</p>

</div>

<div class="profile">

<div class="profile-icon">

<?php
echo htmlspecialchars(
    strtoupper(substr($user_name, 0, 1))
);
?>

</div>

<div>

<div class="profile-name">
<?php echo htmlspecialchars($user_name); ?>
</div>

<div class="profile-email">
<?php echo htmlspecialchars($user_email); ?>
</div>

</div>

</div>

</div>

<div class="quick-action">

<div class="hero">

<h2>
Need to report a cybercrime?
</h2>

<p>
Submit your complaint through our secure reporting system,
provide the required information and supporting details,
and track its progress from your dashboard.
</p>

<a
    href="../report-crime.php"
    class="report-btn"
>
 Report Cyber Crime
</a>

</div>

<div class="security-card">



<h3>
Stay Cyber Safe
</h3>

<p>
Never share your passwords, OTPs, banking PINs or
other sensitive information with unknown persons online.
</p>

</div>

</div>

<div class="stats">

<div class="stat-card">

<div class="stat-top">

<div>

<div class="stat-number">
<?php echo $totalComplaints; ?>
</div>

<div class="stat-title">
Total Complaints
</div>

</div>

</div>

</div>

<div class="stat-card">

<div class="stat-top">

<div>

<div class="stat-number">
<?php echo $pendingComplaints; ?>
</div>

<div class="stat-title">
Active Complaints
</div>

</div>


</div>

</div>

<div class="stat-card">

<div class="stat-top">

<div>

<div class="stat-number">
<?php echo $resolvedComplaints; ?>
</div>

<div class="stat-title">
Resolved
</div>

</div>


</div>

</div>

<div class="stat-card">

<div class="stat-top">

<div>

<div class="stat-number">
<?php echo $closedComplaints; ?>
</div>

<div class="stat-title">
Closed
</div>

</div>

</div>

</div>

</div>

</main>

</body>

</html>