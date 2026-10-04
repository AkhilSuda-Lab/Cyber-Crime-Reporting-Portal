<?php

require_once "../includes/admin-auth.php";
requireAdminLogin();

require_once "../config/database.php";

$search = trim($_GET["search"] ?? "");
$status_filter = trim($_GET["status"] ?? "");

$where = [];
$params = [];
$types = "";

if ($search !== "") {
    $searchTerm = "%" . $search . "%";

    $where[] = "(
        c.complaint_id LIKE ?
        OR u.full_name LIKE ?
        OR u.email LIKE ?
        OR c.category LIKE ?
        OR c.location LIKE ?
    )";

    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;

    $types .= "sssss";
}

if ($status_filter !== "") {
    $where[] = "c.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$sql = "
    SELECT
        c.id,
        c.complaint_id,
        c.category,
        c.incident_date,
        c.incident_time,
        c.location,
        c.amount,
        c.status,
        c.created_at,
        u.full_name,
        u.email
    FROM complaints c
    LEFT JOIN users u
        ON c.user_id = u.id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY c.id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Complaints | Cyber Crime Reporting Portal</title>

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

.filter-box {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(15, 23, 42, 0.06);
    margin-bottom: 20px;
}

.filter-form {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-form input,
.filter-form select {
    padding: 12px 15px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
}

.filter-form input {
    flex: 1;
    min-width: 250px;
}

.filter-form input:focus,
.filter-form select:focus {
    border-color: #2563eb;
}

.filter-form button {
    background: #2563eb;
    color: white;
    border: none;
    padding: 12px 22px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
}

.filter-form button:hover {
    background: #1d4ed8;
}

.clear-btn {
    background: #64748b;
    color: white;
    text-decoration: none;
    padding: 12px 18px;
    border-radius: 8px;
    display: flex;
    align-items: center;
}

.clear-btn:hover {
    background: #475569;
}

.total-box {
    color: #64748b;
    font-size: 14px;
    margin-bottom: 15px;
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
    min-width: 1100px;
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

.complaint-id {
    font-weight: bold;
    color: #2563eb;
}

.user-name {
    font-weight: bold;
}

.category {
    color: #475569;
}

.amount {
    font-weight: bold;
}

.status {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 11px;
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
    background: #ede9fe;
    color: #6d28d9;
}

.status-investigation {
    background: #cffafe;
    color: #155e75;
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
    background: #2563eb;
    color: white;
    text-decoration: none;
    padding: 7px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: bold;
}

.view-btn:hover {
    background: #1d4ed8;
}

.no-complaints {
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

    .filter-form {
        flex-direction: column;
    }

    .filter-form input,
    .filter-form select {
        width: 100%;
        min-width: 0;
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

<a href="complaints.php" class="active">
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

<div class="header">

<h1>Manage Complaints</h1>

<p>
View, search and manage cyber crime complaints submitted by users.
</p>

</div>

<div class="filter-box">

<form method="GET" class="filter-form">

<input
type="text"
name="search"
placeholder="Search complaint ID, user, email, category or location..."
value="<?php echo htmlspecialchars($search); ?>"
>

<select name="status">

<option value="">
All Statuses
</option>

<option value="Submitted"
<?php echo $status_filter === "Submitted" ? "selected" : ""; ?>>
Submitted
</option>

<option value="Under Review"
<?php echo $status_filter === "Under Review" ? "selected" : ""; ?>>
Under Review
</option>

<option value="Assigned"
<?php echo $status_filter === "Assigned" ? "selected" : ""; ?>>
Assigned
</option>

<option value="Investigation"
<?php echo $status_filter === "Investigation" ? "selected" : ""; ?>>
Investigation
</option>

<option value="Resolved"
<?php echo $status_filter === "Resolved" ? "selected" : ""; ?>>
Resolved
</option>

<option value="Closed"
<?php echo $status_filter === "Closed" ? "selected" : ""; ?>>
Closed
</option>

<option value="Rejected"
<?php echo $status_filter === "Rejected" ? "selected" : ""; ?>>
Rejected
</option>

</select>

<button type="submit">
 Search
</button>

<?php if ($search !== "" || $status_filter !== ""): ?>

<a href="complaints.php" class="clear-btn">
Clear
</a>

<?php endif; ?>

</form>

</div>

<div class="total-box">

<?php
echo "Complaints found: " . $result->num_rows;
?>

</div>

<div class="table-container">

<table>

<thead>

<tr>

<th>ID</th>
<th>Complaint ID</th>
<th>Complainant</th>
<th>Category</th>
<th>Incident Date</th>
<th>Incident Time</th>
<th>Location</th>
<th>Amount</th>
<th>Status</th>
<th>Submitted On</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php if ($result->num_rows > 0): ?>

<?php while ($complaint = $result->fetch_assoc()): ?>

<tr>

<td>
<?php echo (int)$complaint["id"]; ?>
</td>

<td class="complaint-id">
<?php echo htmlspecialchars($complaint["complaint_id"]); ?>
</td>

<td class="user-name">
<?php echo htmlspecialchars($complaint["full_name"] ?? "Unknown"); ?>
</td>

<td class="category">
<?php echo htmlspecialchars($complaint["category"]); ?>
</td>

<td>
<?php echo htmlspecialchars($complaint["incident_date"]); ?>
</td>

<td>
<?php echo htmlspecialchars($complaint["incident_time"] ?? "-"); ?>
</td>

<td>
<?php echo htmlspecialchars($complaint["location"] ?? "-"); ?>
</td>

<td class="amount">
₹<?php echo number_format((float)$complaint["amount"], 2); ?>
</td>

<td>

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

<span class="status <?php echo $statusClass; ?>">

<?php echo htmlspecialchars($complaint["status"]); ?>

</span>

</td>

<td>
<?php echo htmlspecialchars($complaint["created_at"]); ?>
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

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="11" class="no-complaints">

No complaints found.

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

$stmt->close();
$conn->close();

?>