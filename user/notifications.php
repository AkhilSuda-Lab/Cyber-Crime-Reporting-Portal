<?php
require_once "../includes/auth.php";
requireUserLogin();
require_once "../config/database.php";

$user_id = $_SESSION["user_id"];

$sql = "SELECT id, complaint_id, message, is_read, created_at
        FROM notifications
        WHERE user_id = ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database query failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

$stmt->close();

$update_sql = "UPDATE notifications
               SET is_read = 1
               WHERE user_id = ?";

$update_stmt = $conn->prepare($update_sql);

if ($update_stmt) {
    $update_stmt->bind_param("i", $user_id);
    $update_stmt->execute();
    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notifications</title>
<link rel="stylesheet" href="../assets/css/dashboard.css">

<style>
.notifications-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
}

.notifications-card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
}

.notifications-card h1 {
    margin-top: 0;
    color: #12304d;
}

.notification {
    padding: 18px;
    margin-bottom: 12px;
    border-radius: 8px;
    background: #f7f9fc;
    border-left: 4px solid #1976d2;
}

.notification-message {
    font-size: 16px;
    color: #333;
}

.notification-meta {
    margin-top: 8px;
    font-size: 13px;
    color: #777;
}

.complaint-number {
    font-weight: bold;
    color: #1976d2;
}

.no-notifications {
    padding: 20px 0;
    color: #666;
}

.back-link {
    display: inline-block;
    margin-bottom: 20px;
    color: #1976d2;
    text-decoration: none;
    font-weight: bold;
}
</style>
</head>

<body>

<div class="notifications-container">

<a href="dashboard.php" class="back-link">
← Back to Dashboard
</a>

<div class="notifications-card">

<h1>Notifications</h1>

<?php if (count($notifications) > 0): ?>

<?php foreach ($notifications as $notification): ?>

<div class="notification">

<div class="notification-message">
<?php echo nl2br(htmlspecialchars($notification["message"])); ?>
</div>

<div class="notification-meta">

<?php if (!empty($notification["complaint_id"])): ?>

<span class="complaint-number">
Complaint ID: <?php echo htmlspecialchars($notification["complaint_id"]); ?>
</span>

<br>

<?php endif; ?>

<?php echo htmlspecialchars($notification["created_at"]); ?>

</div>

</div>

<?php endforeach; ?>

<?php else: ?>

<div class="no-notifications">
You have no notifications at the moment.
</div>

<?php endif; ?>

</div>

</div>

</body>
</html>