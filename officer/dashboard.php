<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["officer_id"])) {
    header("Location: login.php");
    exit;
}

$officerId = (int)$_SESSION["officer_id"];
$officerName = $_SESSION["officer_name"] ?? "Officer";

$assigned = 0;
$investigation = 0;
$resolved = 0;

$stmt = $conn->prepare(
    "SELECT
        COUNT(*) AS total,
        SUM(status = 'Investigation') AS investigation,
        SUM(status = 'Resolved') AS resolved
     FROM complaints
     WHERE assigned_officer_id = ?"
);

$stmt->bind_param("i", $officerId);
$stmt->execute();

$result = $stmt->get_result();
$stats = $result->fetch_assoc();

$assigned = (int)($stats["total"] ?? 0);
$investigation = (int)($stats["investigation"] ?? 0);
$resolved = (int)($stats["resolved"] ?? 0);

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Officer Dashboard | Cyber Crime Reporting Portal</title>

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

.welcome {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(15,23,42,.06);
    margin-bottom: 25px;
}

.welcome h2 {
    margin-bottom: 8px;
}

.welcome p {
    color: #64748b;
}

.cards {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 20px;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(15,23,42,.06);
}

.card-icon {
    font-size: 30px;
    margin-bottom: 15px;
}

.card h3 {
    font-size: 15px;
    color: #64748b;
    margin-bottom: 8px;
}

.card p {
    font-size: 30px;
    font-weight: bold;
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

    .cards {
        grid-template-columns: 1fr;
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

<a href="dashboard.php" class="active">
 &nbsp; Dashboard
</a>

<a href="complaints.php">
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

<h1>Officer Dashboard</h1>

<p>Manage and investigate your assigned cyber crime complaints.</p>

</div>

<div class="welcome">

<h2>
Welcome, <?php echo htmlspecialchars($officerName); ?> 
</h2>

<p>
Here is an overview of the complaints currently assigned to you.
</p>

</div>

<div class="cards">

<div class="card">



<h3>Assigned Complaints</h3>

<p><?php echo $assigned; ?></p>

</div>

<div class="card">



<h3>Under Investigation</h3>

<p><?php echo $investigation; ?></p>

</div>

<div class="card">



<h3>Resolved</h3>

<p><?php echo $resolved; ?></p>

</div>

</div>

</main>

</body>

</html>