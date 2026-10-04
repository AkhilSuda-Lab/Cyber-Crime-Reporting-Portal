<?php
require_once "../includes/auth.php";
requireUserLogin();
require_once "../config/database.php";

$user_id = $_SESSION["user_id"];
$complaint_db_id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($complaint_db_id <= 0) {
    header("Location: my-complaints.php");
    exit();
}

$sql = "SELECT id, complaint_id, category, status, created_at, updated_at
        FROM complaints
        WHERE id = ? AND user_id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database query failed: " . $conn->error);
}

$stmt->bind_param("ii", $complaint_db_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    header("Location: my-complaints.php");
    exit();
}

$complaint = $result->fetch_assoc();
$stmt->close();

$update_sql = "SELECT status, remarks, created_at
               FROM complaint_updates
               WHERE complaint_id = ?
               ORDER BY created_at ASC";

$update_stmt = $conn->prepare($update_sql);
$updates = [];

if ($update_stmt) {
    $update_stmt->bind_param("i", $complaint_db_id);
    $update_stmt->execute();

    $update_result = $update_stmt->get_result();

    while ($row = $update_result->fetch_assoc()) {
        $updates[] = $row;
    }

    $update_stmt->close();
}

$conn->close();

$status_class = strtolower(
    str_replace(" ", "-", $complaint["status"])
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complaint Status</title>

<link rel="stylesheet" href="../assets/css/dashboard.css">

<style>
.status-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.back-btn {
    display: inline-block;
    margin-bottom: 25px;
    color: #1976d2;
    text-decoration: none;
    font-weight: bold;
}

.status-card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.status-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.status-header h1 {
    margin: 0;
    color: #12304d;
}

.complaint-id {
    margin-top: 8px;
    color: #666;
}

.status {
    padding: 9px 16px;
    border-radius: 20px;
    font-weight: bold;
}

.submitted {
    background: #e3f2fd;
    color: #1976d2;
}

.under-review {
    background: #fff3cd;
    color: #856404;
}

.assigned {
    background: #e8eaf6;
    color: #3949ab;
}

.investigation {
    background: #f3e5f5;
    color: #7b1fa2;
}

.resolved {
    background: #e8f5e9;
    color: #2e7d32;
}

.closed {
    background: #eee;
    color: #555;
}

.rejected {
    background: #ffebee;
    color: #c62828;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-top: 25px;
}

.info-box {
    background: #f7f9fc;
    padding: 15px;
    border-radius: 8px;
}

.info-label {
    display: block;
    color: #666;
    font-size: 13px;
    margin-bottom: 5px;
}

.info-value {
    font-weight: 600;
}

.section-title {
    color: #12304d;
    margin-bottom: 25px;
}

.timeline {
    position: relative;
    margin-left: 10px;
    padding-left: 30px;
    border-left: 3px solid #d9e2ec;
}

.timeline-item {
    position: relative;
    margin-bottom: 25px;
}

.timeline-item::before {
    content: "";
    position: absolute;
    left: -39px;
    top: 3px;
    width: 14px;
    height: 14px;
    background: #1976d2;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #1976d2;
}

.timeline-status {
    font-size: 17px;
    font-weight: bold;
    color: #12304d;
}

.timeline-date {
    font-size: 13px;
    color: #777;
    margin: 5px 0 8px;
}

.timeline-remarks {
    background: #f7f9fc;
    padding: 12px;
    border-radius: 7px;
    color: #444;
}

.no-updates {
    color: #666;
    padding: 10px 0;
}

.action-buttons {
    margin-top: 25px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.action-btn {
    display: inline-block;
    padding: 10px 18px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
}

.details-btn {
    background: #1976d2;
    color: #fff;
}

.complaints-btn {
    background: #e9eef5;
    color: #12304d;
}

@media (max-width: 700px) {
    .info-grid {
        grid-template-columns: 1fr;
    }

    .status-card {
        padding: 20px;
    }
}
</style>
</head>

<body>

<div class="status-container">

<a href="my-complaints.php" class="back-btn">
← Back to My Complaints
</a>

<div class="status-card">

<div class="status-header">

<div>
<h1>Complaint Status</h1>

<div class="complaint-id">
Complaint ID:
<strong>
<?php echo htmlspecialchars($complaint["complaint_id"]); ?>
</strong>
</div>
</div>

<span class="status <?php echo htmlspecialchars($status_class); ?>">
<?php echo htmlspecialchars($complaint["status"]); ?>
</span>

</div>

<div class="info-grid">

<div class="info-box">
<span class="info-label">Category</span>
<span class="info-value">
<?php echo htmlspecialchars($complaint["category"]); ?>
</span>
</div>

<div class="info-box">
<span class="info-label">Submitted On</span>
<span class="info-value">
<?php echo htmlspecialchars($complaint["created_at"]); ?>
</span>
</div>

<div class="info-box">
<span class="info-label">Last Updated</span>
<span class="info-value">
<?php echo htmlspecialchars($complaint["updated_at"]); ?>
</span>
</div>

</div>

<div class="action-buttons">

<a
href="complaint-details.php?id=<?php echo $complaint_db_id; ?>"
class="action-btn details-btn"
>
View Complaint Details
</a>

<a
href="my-complaints.php"
class="action-btn complaints-btn"
>
My Complaints
</a>

</div>

</div>

<div class="status-card">

<h2 class="section-title">
Complaint Progress
</h2>

<?php if (count($updates) > 0): ?>

<div class="timeline">

<?php foreach ($updates as $update): ?>

<div class="timeline-item">

<div class="timeline-status">
<?php echo htmlspecialchars($update["status"]); ?>
</div>

<div class="timeline-date">
<?php echo htmlspecialchars($update["created_at"]); ?>
</div>

<div class="timeline-remarks">

<?php
if (!empty($update["remarks"])) {
    echo nl2br(
        htmlspecialchars($update["remarks"])
    );
} else {
    echo "No remarks provided.";
}
?>

</div>

</div>

<?php endforeach; ?>

</div>

<?php else: ?>

<div class="no-updates">
No status updates have been added yet.
</div>

<?php endif; ?>

</div>

</div>

</body>
</html>