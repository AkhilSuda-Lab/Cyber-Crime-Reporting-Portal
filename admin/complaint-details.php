<?php

require_once "../includes/admin-auth.php";
requireAdminLogin();

require_once "../config/database.php";

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: complaints.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT
        c.*,
        u.full_name,
        u.email,
        u.mobile,
        u.address,
        u.city,
        u.state,
        o.name AS officer_name,
        o.email AS officer_email,
        o.mobile AS officer_mobile,
        o.department AS officer_department
     FROM complaints c
     LEFT JOIN users u ON c.user_id = u.id
     LEFT JOIN officers o ON c.assigned_officer_id = o.id
     WHERE c.id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    header("Location: complaints.php");
    exit;
}

$complaint = $result->fetch_assoc();

$stmt->close();

$officers = [];

$officerStmt = $conn->prepare(
    "SELECT id, name, email, mobile, department
     FROM officers
     WHERE status = 'Active'
     ORDER BY name ASC"
);

$officerStmt->execute();

$officerResult = $officerStmt->get_result();

while ($row = $officerResult->fetch_assoc()) {
    $officers[] = $row;
}

$officerStmt->close();
$conn->close();

$success = $_SESSION["admin_complaint_success"] ?? "";
$error = $_SESSION["admin_complaint_error"] ?? "";

unset($_SESSION["admin_complaint_success"]);
unset($_SESSION["admin_complaint_error"]);

function valueOrDash($value)
{
    return ($value !== null && $value !== "")
        ? htmlspecialchars($value)
        : "-";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Complaint Details | Cyber Crime Reporting Portal</title>

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

.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.top-bar h1 {
    font-size: 27px;
}

.back-btn {
    background: #64748b;
    color: white;
    text-decoration: none;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 14px;
}

.back-btn:hover {
    background: #475569;
}

.card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 3px 15px rgba(15, 23, 42, 0.06);
}

.card h2 {
    font-size: 19px;
    margin-bottom: 20px;
    color: #0f172a;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 12px;
}

.grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

.field {
    background: #f8fafc;
    padding: 14px;
    border-radius: 9px;
}

.label {
    display: block;
    color: #64748b;
    font-size: 12px;
    margin-bottom: 6px;
    font-weight: bold;
    text-transform: uppercase;
}

.value {
    color: #1e293b;
    font-size: 14px;
    word-break: break-word;
}

.full {
    grid-column: 1 / -1;
}

.description {
    line-height: 1.7;
    white-space: pre-wrap;
}

.complaint-id {
    color: #2563eb;
    font-weight: bold;
    font-size: 16px;
}

.status {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 20px;
    background: #dbeafe;
    color: #1d4ed8;
    font-size: 12px;
    font-weight: bold;
}

.amount {
    font-weight: bold;
    color: #166534;
}

.status-form,
.officer-form {
    display: flex;
    gap: 12px;
    align-items: center;
}

.status-form select,
.officer-form select {
    flex: 1;
    padding: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    color: #1e293b;
    outline: none;
}

.status-form select:focus,
.officer-form select:focus {
    border-color: #2563eb;
}

.status-form button,
.officer-form button {
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    background: #2563eb;
    color: white;
    font-weight: bold;
    cursor: pointer;
}

.status-form button:hover,
.officer-form button:hover {
    background: #1d4ed8;
}

.success-message {
    background: #dcfce7;
    color: #166534;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    border: 1px solid #bbf7d0;
}

.error-message {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    border: 1px solid #fecaca;
}

.assigned-box {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 18px;
}

.assigned-box strong {
    color: #1d4ed8;
}

.no-officer {
    color: #64748b;
    background: #f8fafc;
    padding: 14px;
    border-radius: 8px;
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

    .grid {
        grid-template-columns: 1fr;
    }

    .full {
        grid-column: auto;
    }

    .top-bar {
        gap: 15px;
        align-items: flex-start;
        flex-direction: column;
    }

    .status-form,
    .officer-form {
        flex-direction: column;
        align-items: stretch;
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

<div class="top-bar">

<h1>Complaint Details</h1>

<a href="complaints.php" class="back-btn">
← Back to Complaints
</a>

</div>

<?php if ($success !== ""): ?>

<div class="success-message">
<?php echo htmlspecialchars($success); ?>
</div>

<?php endif; ?>

<?php if ($error !== ""): ?>

<div class="error-message">
<?php echo htmlspecialchars($error); ?>
</div>

<?php endif; ?>

<div class="card">

<h2>Complaint Information</h2>

<div class="grid">

<div class="field">

<span class="label">Complaint ID</span>

<div class="value complaint-id">
<?php echo valueOrDash($complaint["complaint_id"]); ?>
</div>

</div>

<div class="field">

<span class="label">Current Status</span>

<div class="value">

<span class="status">
<?php echo valueOrDash($complaint["status"]); ?>
</span>

</div>

</div>

<div class="field">

<span class="label">Crime Category</span>

<div class="value">
<?php echo valueOrDash($complaint["category"]); ?>
</div>

</div>

<div class="field">

<span class="label">Incident Date</span>

<div class="value">
<?php echo valueOrDash($complaint["incident_date"]); ?>
</div>

</div>

<div class="field">

<span class="label">Incident Time</span>

<div class="value">
<?php echo valueOrDash($complaint["incident_time"]); ?>
</div>

</div>

<div class="field">

<span class="label">Incident Location</span>

<div class="value">
<?php echo valueOrDash($complaint["location"]); ?>
</div>

</div>

<div class="field">

<span class="label">Financial Loss</span>

<div class="value amount">
₹<?php echo number_format((float)$complaint["amount"], 2); ?>
</div>

</div>

<div class="field">

<span class="label">Assigned Officer ID</span>

<div class="value">
<?php echo valueOrDash($complaint["assigned_officer_id"]); ?>
</div>

</div>

<div class="field full">

<span class="label">Incident Description</span>

<div class="value description">
<?php echo valueOrDash($complaint["description"]); ?>
</div>

</div>

</div>

</div>

<div class="card">

<h2>Update Complaint Status</h2>

<form
    action="../process/admin-update-complaint-status.php"
    method="POST"
>

<input
    type="hidden"
    name="id"
    value="<?php echo (int)$complaint["id"]; ?>"
>

<div class="status-form">

<select name="status" required>

<option value="Submitted"
<?php echo $complaint["status"] === "Submitted" ? "selected" : ""; ?>>
Submitted
</option>

<option value="Under Review"
<?php echo $complaint["status"] === "Under Review" ? "selected" : ""; ?>>
Under Review
</option>

<option value="Assigned"
<?php echo $complaint["status"] === "Assigned" ? "selected" : ""; ?>>
Assigned
</option>

<option value="Investigation"
<?php echo $complaint["status"] === "Investigation" ? "selected" : ""; ?>>
Investigation
</option>

<option value="Resolved"
<?php echo $complaint["status"] === "Resolved" ? "selected" : ""; ?>>
Resolved
</option>

<option value="Closed"
<?php echo $complaint["status"] === "Closed" ? "selected" : ""; ?>>
Closed
</option>

<option value="Rejected"
<?php echo $complaint["status"] === "Rejected" ? "selected" : ""; ?>>
Rejected
</option>

</select>

<button type="submit">
Update Status
</button>

</div>

</form>

</div>

<div class="card">

<h2>Assign Complaint to Officer</h2>

<?php if (!empty($complaint["assigned_officer_id"])): ?>

<div class="assigned-box">

<strong>Currently Assigned Officer:</strong>

<?php echo valueOrDash($complaint["officer_name"]); ?>

<br>

Email:
<?php echo valueOrDash($complaint["officer_email"]); ?>

<br>

Department:
<?php echo valueOrDash($complaint["officer_department"]); ?>

</div>

<?php else: ?>

<div class="no-officer">
No officer is currently assigned to this complaint.
</div>

<?php endif; ?>

<?php if (count($officers) > 0): ?>

<form
    action="../process/admin-assign-officer.php"
    method="POST"
>

<input
    type="hidden"
    name="complaint_id"
    value="<?php echo (int)$complaint["id"]; ?>"
>

<div class="officer-form">

<select name="officer_id" required>

<option value="">
Select an active officer
</option>

<?php foreach ($officers as $officer): ?>

<option
    value="<?php echo (int)$officer["id"]; ?>"
    <?php echo ((int)$complaint["assigned_officer_id"] === (int)$officer["id"]) ? "selected" : ""; ?>
>

<?php echo htmlspecialchars($officer["name"]); ?>

<?php if (!empty($officer["department"])): ?>
 - <?php echo htmlspecialchars($officer["department"]); ?>
<?php endif; ?>

</option>

<?php endforeach; ?>

</select>

<button type="submit">
Assign Officer
</button>

</div>

</form>

<?php else: ?>

<div class="no-officer">
No active officers are available.
</div>

<?php endif; ?>

</div>

<div class="card">

<h2>Complainant Information</h2>

<div class="grid">

<div class="field">

<span class="label">Full Name</span>

<div class="value">
<?php echo valueOrDash($complaint["full_name"]); ?>
</div>

</div>

<div class="field">

<span class="label">Email</span>

<div class="value">
<?php echo valueOrDash($complaint["email"]); ?>
</div>

</div>

<div class="field">

<span class="label">Mobile</span>

<div class="value">
<?php echo valueOrDash($complaint["mobile"]); ?>
</div>

</div>

<div class="field">

<span class="label">User ID</span>

<div class="value">
<?php echo valueOrDash($complaint["user_id"]); ?>
</div>

</div>

<div class="field">

<span class="label">Address</span>

<div class="value">
<?php echo valueOrDash($complaint["address"]); ?>
</div>

</div>

<div class="field">

<span class="label">City</span>

<div class="value">
<?php echo valueOrDash($complaint["city"]); ?>
</div>

</div>

<div class="field">

<span class="label">State</span>

<div class="value">
<?php echo valueOrDash($complaint["state"]); ?>
</div>

</div>

</div>

</div>

<div class="card">

<h2>Suspect Information</h2>

<div class="grid">

<div class="field">

<span class="label">Suspect Name</span>

<div class="value">
<?php echo valueOrDash($complaint["suspect_name"]); ?>
</div>

</div>

<div class="field">

<span class="label">Suspect Mobile</span>

<div class="value">
<?php echo valueOrDash($complaint["suspect_mobile"]); ?>
</div>

</div>

<div class="field">

<span class="label">Suspect Email</span>

<div class="value">
<?php echo valueOrDash($complaint["suspect_email"]); ?>
</div>

</div>

<div class="field">

<span class="label">Suspect Website</span>

<div class="value">
<?php echo valueOrDash($complaint["suspect_website"]); ?>
</div>

</div>

<div class="field full">

<span class="label">Suspect Social Media</span>

<div class="value">
<?php echo valueOrDash($complaint["suspect_social_media"]); ?>
</div>

</div>

</div>

</div>

<div class="card">

<h2>System Information</h2>

<div class="grid">

<div class="field">

<span class="label">Complaint Created</span>

<div class="value">
<?php echo valueOrDash($complaint["created_at"]); ?>
</div>

</div>

<div class="field">

<span class="label">Last Updated</span>

<div class="value">
<?php echo valueOrDash($complaint["updated_at"]); ?>
</div>

</div>

<div class="field">

<span class="label">Database ID</span>

<div class="value">
<?php echo valueOrDash($complaint["id"]); ?>
</div>

</div>

</div>

</div>

</main>

</body>

</html>