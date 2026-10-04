<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["officer_id"])) {
    header("Location: login.php");
    exit;
}

$officerId = (int)$_SESSION["officer_id"];
$complaintId = (int)($_GET["id"] ?? 0);

if ($complaintId <= 0) {
    header("Location: complaints.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT c.*,u.full_name,u.email,u.mobile,u.address,u.city,u.state
     FROM complaints c
     LEFT JOIN users u ON c.user_id=u.id
     WHERE c.id=? AND c.assigned_officer_id=?
     LIMIT 1"
);

$stmt->bind_param("ii",$complaintId,$officerId);
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
$conn->close();

$success = $_SESSION["officer_success"] ?? "";
$error = $_SESSION["officer_error"] ?? "";

unset($_SESSION["officer_success"]);
unset($_SESSION["officer_error"]);

function showValue($value)
{
    if ($value === null || $value === "") {
        return "-";
    }

    return htmlspecialchars($value);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1.0">

<title>Complaint Details | Officer Panel</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Arial,Helvetica,sans-serif;
    background:#f4f7fb;
    color:#1e293b;
}

.sidebar{
    position:fixed;
    left:0;
    top:0;
    width:250px;
    height:100vh;
    background:#0f172a;
    color:white;
    padding:25px 18px;
}

.logo{
    text-align:center;
    margin-bottom:35px;
}

.logo-icon{
    width:55px;
    height:55px;
    margin:auto;
    background:#2563eb;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:26px;
}

.logo h2{
    margin-top:12px;
    font-size:18px;
}

.logo p{
    color:#94a3b8;
    font-size:12px;
    margin-top:5px;
}

.nav{
    display:flex;
    flex-direction:column;
    gap:8px;
}

.nav a{
    color:#cbd5e1;
    text-decoration:none;
    padding:13px 15px;
    border-radius:9px;
    font-size:14px;
}

.nav a:hover,
.nav a.active{
    background:#2563eb;
    color:white;
}

.nav a.logout{
    margin-top:20px;
    background:#dc2626;
    color:white;
}

.main{
    margin-left:250px;
    padding:30px;
}

.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.top-bar h1{
    font-size:28px;
}

.back-btn{
    background:#64748b;
    color:white;
    text-decoration:none;
    padding:10px 16px;
    border-radius:8px;
    font-size:14px;
}

.card{
    background:white;
    border-radius:15px;
    padding:25px;
    margin-bottom:20px;
    box-shadow:0 3px 15px rgba(15,23,42,.06);
}

.card h2{
    font-size:19px;
    margin-bottom:20px;
    padding-bottom:12px;
    border-bottom:1px solid #e2e8f0;
}

.grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:18px;
}

.field{
    background:#f8fafc;
    padding:14px;
    border-radius:9px;
}

.full{
    grid-column:1/-1;
}

.label{
    display:block;
    color:#64748b;
    font-size:11px;
    font-weight:bold;
    text-transform:uppercase;
    margin-bottom:6px;
}

.value{
    font-size:14px;
    word-break:break-word;
}

.description{
    line-height:1.7;
    white-space:pre-wrap;
}

.complaint-id{
    color:#2563eb;
    font-weight:bold;
}

.status{
    display:inline-block;
    padding:7px 12px;
    border-radius:20px;
    background:#dbeafe;
    color:#1d4ed8;
    font-size:12px;
    font-weight:bold;
}

.amount{
    color:#166534;
    font-weight:bold;
}

.notice{
    background:#eff6ff;
    border:1px solid #bfdbfe;
    color:#1e40af;
    padding:14px;
    border-radius:9px;
    margin-bottom:20px;
}

.success{
    background:#dcfce7;
    border:1px solid #bbf7d0;
    color:#166534;
    padding:12px 15px;
    border-radius:8px;
    margin-bottom:20px;
}

.error{
    background:#fee2e2;
    border:1px solid #fecaca;
    color:#991b1b;
    padding:12px 15px;
    border-radius:8px;
    margin-bottom:20px;
}

.form-group{
    margin-bottom:18px;
}

.form-group label{
    display:block;
    margin-bottom:7px;
    font-size:14px;
    font-weight:bold;
    color:#334155;
}

.form-group select,
.form-group textarea{
    width:100%;
    padding:12px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    font-size:14px;
    outline:none;
    background:white;
}

.form-group select:focus,
.form-group textarea:focus{
    border-color:#2563eb;
}

.form-group textarea{
    min-height:130px;
    resize:vertical;
}

.update-btn{
    border:0;
    padding:13px 22px;
    border-radius:8px;
    background:#2563eb;
    color:white;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
}

.update-btn:hover{
    background:#1d4ed8;
}

@media(max-width:800px){

    .sidebar{
        position:relative;
        width:100%;
        height:auto;
    }

    .main{
        margin-left:0;
        padding:15px;
    }

    .grid{
        grid-template-columns:1fr;
    }

    .full{
        grid-column:auto;
    }

    .top-bar{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
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
📊 &nbsp; Dashboard
</a>

<a href="complaints.php" class="active">
📋 &nbsp; My Complaints
</a>

<a href="profile.php">
👤 &nbsp; My Profile
</a>

<a href="logout.php" class="logout">
🚪 &nbsp; Logout
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

<?php if($success !== ""): ?>

<div class="success">
<?php echo htmlspecialchars($success); ?>
</div>

<?php endif; ?>

<?php if($error !== ""): ?>

<div class="error">
<?php echo htmlspecialchars($error); ?>
</div>

<?php endif; ?>

<div class="notice">
🔒 You are viewing a complaint assigned specifically to your officer account.
</div>

<div class="card">

<h2>Complaint Information</h2>

<div class="grid">

<div class="field">

<span class="label">Complaint ID</span>

<div class="value complaint-id">
<?php echo showValue($complaint["complaint_id"]); ?>
</div>

</div>

<div class="field">

<span class="label">Status</span>

<div class="value">

<span class="status">
<?php echo showValue($complaint["status"]); ?>
</span>

</div>

</div>

<div class="field">

<span class="label">Crime Category</span>

<div class="value">
<?php echo showValue($complaint["category"]); ?>
</div>

</div>

<div class="field">

<span class="label">Incident Date</span>

<div class="value">
<?php echo showValue($complaint["incident_date"]); ?>
</div>

</div>

<div class="field">

<span class="label">Incident Time</span>

<div class="value">
<?php echo showValue($complaint["incident_time"]); ?>
</div>

</div>

<div class="field">

<span class="label">Location</span>

<div class="value">
<?php echo showValue($complaint["location"]); ?>
</div>

</div>

<div class="field">

<span class="label">Financial Loss</span>

<div class="value amount">
₹<?php echo number_format((float)$complaint["amount"],2); ?>
</div>

</div>

<div class="field">

<span class="label">Assigned Officer ID</span>

<div class="value">
<?php echo showValue($complaint["assigned_officer_id"]); ?>
</div>

</div>

<div class="field full">

<span class="label">Complaint Description</span>

<div class="value description">
<?php echo showValue($complaint["description"]); ?>
</div>

</div>

</div>

</div>

<div class="card">

<h2>Complainant Information</h2>

<div class="grid">

<div class="field">

<span class="label">Full Name</span>

<div class="value">
<?php echo showValue($complaint["full_name"]); ?>
</div>

</div>

<div class="field">

<span class="label">Email</span>

<div class="value">
<?php echo showValue($complaint["email"]); ?>
</div>

</div>

<div class="field">

<span class="label">Mobile</span>

<div class="value">
<?php echo showValue($complaint["mobile"]); ?>
</div>

</div>

<div class="field">

<span class="label">User ID</span>

<div class="value">
<?php echo showValue($complaint["user_id"]); ?>
</div>

</div>

<div class="field full">

<span class="label">Address</span>

<div class="value">
<?php echo showValue($complaint["address"]); ?>
</div>

</div>

<div class="field">

<span class="label">City</span>

<div class="value">
<?php echo showValue($complaint["city"]); ?>
</div>

</div>

<div class="field">

<span class="label">State</span>

<div class="value">
<?php echo showValue($complaint["state"]); ?>
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
<?php echo showValue($complaint["suspect_name"]); ?>
</div>

</div>

<div class="field">

<span class="label">Suspect Mobile</span>

<div class="value">
<?php echo showValue($complaint["suspect_mobile"]); ?>
</div>

</div>

<div class="field">

<span class="label">Suspect Email</span>

<div class="value">
<?php echo showValue($complaint["suspect_email"]); ?>
</div>

</div>

<div class="field">

<span class="label">Suspect Website</span>

<div class="value">
<?php echo showValue($complaint["suspect_website"]); ?>
</div>

</div>

<div class="field full">

<span class="label">Suspect Social Media</span>

<div class="value">
<?php echo showValue($complaint["suspect_social_media"]); ?>
</div>

</div>

</div>

</div>

<div class="card">

<h2>Investigation Update</h2>

<form action="../process/officer-update-complaint.php" method="POST">

<input
    type="hidden"
    name="complaint_id"
    value="<?php echo (int)$complaint["id"]; ?>"
>

<div class="form-group">

<label for="status">
Complaint Status
</label>

<select name="status" id="status" required>

<option value="Investigation" <?php echo $complaint["status"] === "Investigation" ? "selected" : ""; ?>>
Investigation
</option>

<option value="Resolved" <?php echo $complaint["status"] === "Resolved" ? "selected" : ""; ?>>
Resolved
</option>

<option value="Closed" <?php echo $complaint["status"] === "Closed" ? "selected" : ""; ?>>
Closed
</option>

</select>

</div>

<div class="form-group">

<label for="investigation_remarks">
Investigation Remarks
</label>

<textarea
    name="investigation_remarks"
    id="investigation_remarks"
    placeholder="Enter investigation findings, actions taken, or important remarks..."
><?php echo htmlspecialchars($complaint["investigation_remarks"] ?? ""); ?></textarea>

</div>

<button type="submit" class="update-btn">
💾 Save Investigation Update
</button>

</form>

</div>

<div class="card">

<h2>System Information</h2>

<div class="grid">

<div class="field">

<span class="label">Created At</span>

<div class="value">
<?php echo showValue($complaint["created_at"]); ?>
</div>

</div>

<div class="field">

<span class="label">Last Updated</span>

<div class="value">
<?php echo showValue($complaint["updated_at"]); ?>
</div>

</div>

</div>

</div>

</main>

</body>

</html>