<?php
require_once "../includes/admin-auth.php";
requireAdminLogin();

require_once "../config/database.php";

$search = trim($_GET["search"] ?? "");

if ($search !== "") {

    $searchTerm = "%" . $search . "%";

    $stmt = $conn->prepare(
        "SELECT id, full_name, email, mobile, address, city, state, status, created_at
         FROM users
         WHERE full_name LIKE ?
         OR email LIKE ?
         OR mobile LIKE ?
         OR city LIKE ?
         OR state LIKE ?
         ORDER BY id DESC"
    );

    $stmt->bind_param(
        "sssss",
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

    $stmt->execute();
    $result = $stmt->get_result();

} else {

    $result = $conn->query(
        "SELECT id, full_name, email, mobile, address, city, state, status, created_at
         FROM users
         ORDER BY id DESC"
    );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Users | Cyber Crime Reporting Portal</title>

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
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(15, 23, 42, 0.06);
    margin-bottom: 25px;
}

.header h1 {
    font-size: 26px;
    margin-bottom: 7px;
}

.header p {
    color: #64748b;
}

.search-box {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(15, 23, 42, 0.06);
    margin-bottom: 20px;
}

.search-form {
    display: flex;
    gap: 10px;
}

.search-form input {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
}

.search-form input:focus {
    border-color: #2563eb;
}

.search-form button {
    background: #2563eb;
    color: white;
    border: none;
    padding: 12px 22px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
}

.search-form button:hover {
    background: #1d4ed8;
}

.clear-btn {
    background: #64748b;
    text-decoration: none;
    color: white;
    padding: 12px 18px;
    border-radius: 8px;
    display: flex;
    align-items: center;
}

.clear-btn:hover {
    background: #475569;
}

.total-box {
    margin-bottom: 15px;
    color: #64748b;
    font-size: 14px;
}

.table-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(15, 23, 42, 0.06);
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1200px;
}

thead {
    background: #0f172a;
    color: white;
}

th {
    padding: 15px;
    text-align: left;
    font-size: 13px;
    white-space: nowrap;
}

td {
    padding: 14px 15px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 13px;
    white-space: nowrap;
}

tbody tr:hover {
    background: #f8fafc;
}

.user-name {
    font-weight: bold;
    color: #0f172a;
}

.email {
    color: #2563eb;
}

.status {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
}

.status-active {
    background: #dcfce7;
    color: #166534;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
}

.action-form {
    margin: 0;
}

.action-btn {
    border: none;
    padding: 7px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    font-weight: bold;
}

.action-btn.activate {
    background: #dcfce7;
    color: #166534;
}

.action-btn.activate:hover {
    background: #bbf7d0;
}

.action-btn.deactivate {
    background: #fee2e2;
    color: #991b1b;
}

.action-btn.deactivate:hover {
    background: #fecaca;
}

.no-users {
    text-align: center;
    padding: 40px;
    color: #64748b;
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

    .search-form {
        flex-direction: column;
    }

    .clear-btn {
        justify-content: center;
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

<a href="dashboard.php">
 &nbsp; Dashboard
</a>

<a href="complaints.php">
 &nbsp; Complaints
</a>

<a href="users.php" class="active">
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

<div class="header">

<h1>Manage Users</h1>

<p>
View, search and manage registered users.
</p>

</div>

<div class="search-box">

<form method="GET" class="search-form">

<input
type="text"
name="search"
placeholder="Search by name, email, mobile, city or state..."
value="<?php echo htmlspecialchars($search); ?>"
>

<button type="submit">
 Search
</button>

<?php if ($search !== ""): ?>

<a href="users.php" class="clear-btn">
Clear
</a>

<?php endif; ?>

</form>

</div>

<div class="total-box">

<?php
if ($result) {
    echo "Users found: " . $result->num_rows;
}
?>

</div>

<div class="table-container">

<table>

<thead>

<tr>
<th>ID</th>
<th>Full Name</th>
<th>Email</th>
<th>Mobile</th>
<th>Address</th>
<th>City</th>
<th>State</th>
<th>Status</th>
<th>Registered On</th>
<th>Action</th>
</tr>

</thead>

<tbody>

<?php if ($result && $result->num_rows > 0): ?>

<?php while ($user = $result->fetch_assoc()): ?>

<tr>

<td>
<?php echo (int)$user["id"]; ?>
</td>

<td class="user-name">
<?php echo htmlspecialchars($user["full_name"]); ?>
</td>

<td class="email">
<?php echo htmlspecialchars($user["email"]); ?>
</td>

<td>
<?php echo htmlspecialchars($user["mobile"]); ?>
</td>

<td>
<?php echo htmlspecialchars($user["address"]); ?>
</td>

<td>
<?php echo htmlspecialchars($user["city"]); ?>
</td>

<td>
<?php echo htmlspecialchars($user["state"]); ?>
</td>

<td>

<?php if ($user["status"] === "Active"): ?>

<span class="status status-active">
Active
</span>

<?php else: ?>

<span class="status status-inactive">
Inactive
</span>

<?php endif; ?>

</td>

<td>
<?php echo htmlspecialchars($user["created_at"]); ?>
</td>

<td>

<form
method="POST"
action="../process/admin-user-status.php"
class="action-form"
>

<input
type="hidden"
name="user_id"
value="<?php echo (int)$user["id"]; ?>"
>

<?php if ($user["status"] === "Active"): ?>

<input
type="hidden"
name="status"
value="Inactive"
>

<button
type="submit"
class="action-btn deactivate"
>
Deactivate
</button>

<?php else: ?>

<input
type="hidden"
name="status"
value="Active"
>

<button
type="submit"
class="action-btn activate"
>
Activate
</button>

<?php endif; ?>

</form>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="10" class="no-users">
No users found.
</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</main>

</body>

</html>

<?php

if (isset($stmt)) {
    $stmt->close();
}

$conn->close();

?>