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

$sql = "SELECT id, complaint_id, category, incident_date, incident_time, location, 
description, amount, suspect_name, suspect_mobile, suspect_email, suspect_website, 
suspect_social_media, status, created_at, updated_at FROM complaints WHERE id = ? AND user_id = ? LIMIT 1";
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

$evidence_sql = "SELECT id, file_name, file_path, file_type, file_size, uploaded_at FROM evidence WHERE complaint_id = ? ORDER BY uploaded_at DESC";

$evidence_stmt = $conn->prepare($evidence_sql);
$evidence = [];

if ($evidence_stmt) {
    $evidence_stmt->bind_param("i", $complaint_db_id);
    $evidence_stmt->execute();
    $evidence_result = $evidence_stmt->get_result();

    while ($row = $evidence_result->fetch_assoc()) {
        $evidence[] = $row;
    }

    $evidence_stmt->close();
}

$conn->close();

$status_class = strtolower(str_replace(" ", "-", $complaint["status"]));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complaint Details</title>

<link rel="stylesheet" href="../assets/css/dashboard.css">

<style>
.details-container {
    max-width: 1000px;
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

.details-card {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.details-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.details-header h1 {
    margin: 0;
    color: #12304d;
}

.complaint-id {
    color: #666;
    margin-top: 8px;
}

.status {
    display: inline-block;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 14px;
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

.section-title {
    color: #12304d;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.detail-item {
    padding: 15px;
    background: #f7f9fc;
    border-radius: 8px;
}

.detail-label {
    display: block;
    font-size: 13px;
    color: #666;
    margin-bottom: 5px;
}

.detail-value {
    color: #172033;
    font-weight: 600;
    word-break: break-word;
}

.description-box {
    padding: 18px;
    background: #f7f9fc;
    border-radius: 8px;
    white-space: pre-wrap;
    line-height: 1.7;
}

.evidence-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.evidence-name {
    font-weight: 600;
}

.download-btn {
    display: inline-block;
    padding: 8px 14px;
    background: #1976d2;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
}

.no-evidence {
    color: #666;
    padding: 10px 0;
}

@media (max-width: 700px) {
    .details-grid {
        grid-template-columns: 1fr;
    }

    .details-card {
        padding: 20px;
    }
}
</style>
</head>

<body>

<div class="details-container">
<a href="my-complaints.php" class="back-btn">← Back to My Complaints</a>
<div class="details-card">
<div class="details-header">
<div>
<h1>Complaint Details</h1>
<div class="complaint-id">
Complaint ID:
<strong><?php echo htmlspecialchars($complaint["complaint_id"]); ?></strong>
</div>
</div>

<span class="status <?php echo htmlspecialchars($status_class); ?>">
<?php echo htmlspecialchars($complaint["status"]); ?>
</span>
</div>

<h2 class="section-title">Incident Information</h2>

<div class="details-grid">

<div class="detail-item">
<span class="detail-label">Crime Category</span>
<span class="detail-value">
<?php echo htmlspecialchars($complaint["category"]); ?>
</span>
</div>

<div class="detail-item">
<span class="detail-label">Incident Date</span>
<span class="detail-value">
<?php echo htmlspecialchars($complaint["incident_date"]); ?>
</span>
</div>

<div class="detail-item">
<span class="detail-label">Incident Time</span>
<span class="detail-value">
<?php echo $complaint["incident_time"] ? htmlspecialchars($complaint["incident_time"]) : "Not provided"; ?>
</span>
</div>

<div class="detail-item">
<span class="detail-label">Location</span>
<span class="detail-value">
<?php echo $complaint["location"] ? htmlspecialchars($complaint["location"]) : "Not provided"; ?>
</span>
</div>

<div class="detail-item">
<span class="detail-label">Financial Loss</span>
<span class="detail-value">
₹<?php echo number_format((float)$complaint["amount"], 2); ?>
</span>
</div>

<div class="detail-item">
<span class="detail-label">Submitted On</span>
<span class="detail-value">
<?php echo htmlspecialchars($complaint["created_at"]); ?>
</span>
</div>

</div>

<h2 class="section-title">Incident Description</h2>

<div class="description-box">
<?php echo htmlspecialchars($complaint["description"]); ?>
</div>

</div>

<div class="details-card">

<h2 class="section-title">Suspect Information</h2>

<div class="details-grid">

<div class="detail-item">
<span class="detail-label">Suspect Name</span>
<span class="detail-value">
<?php echo $complaint["suspect_name"] ? htmlspecialchars($complaint["suspect_name"]) : "Not provided"; ?>
</span>
</div>

<div class="detail-item">
<span class="detail-label">Suspect Mobile</span>
<span class="detail-value">
<?php echo $complaint["suspect_mobile"] ? htmlspecialchars($complaint["suspect_mobile"]) : "Not provided"; ?>
</span>
</div>

<div class="detail-item">
<span class="detail-label">Suspect Email</span>
<span class="detail-value">
<?php echo $complaint["suspect_email"] ? htmlspecialchars($complaint["suspect_email"]) : "Not provided"; ?>
</span>
</div>

<div class="detail-item">
<span class="detail-label">Suspect Website</span>
<span class="detail-value">
<?php echo $complaint["suspect_website"] ? htmlspecialchars($complaint["suspect_website"]) : "Not provided"; ?>
</span>
</div>

<div class="detail-item">
<span class="detail-label">Suspect Social Media</span>
<span class="detail-value">
<?php echo $complaint["suspect_social_media"] ? htmlspecialchars($complaint["suspect_social_media"]) : "Not provided"; ?>
</span>
</div>

</div>

</div>

<div class="details-card">

<h2 class="section-title">Evidence</h2>

<?php if (count($evidence) > 0): ?>

<?php foreach ($evidence as $file): ?>

<div class="evidence-item">

<div>
<div class="evidence-name">
<?php echo htmlspecialchars($file["file_name"]); ?>
</div>

<small>
Uploaded:
<?php echo htmlspecialchars($file["uploaded_at"]); ?>
</small>
</div>

<a
href="../<?php echo htmlspecialchars($file["file_path"]); ?>"
target="_blank"
class="download-btn"
>
View Evidence
</a>

</div>

<?php endforeach; ?>

<?php else: ?>

<div class="no-evidence">
No evidence was uploaded with this complaint.
</div>

<?php endif; ?>

</div>

</div>

</body>
</html>