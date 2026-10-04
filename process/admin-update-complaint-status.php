<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../admin/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin/complaints.php");
    exit;
}

$id = (int)($_POST["id"] ?? 0);
$status = trim($_POST["status"] ?? "");
$remarks = trim($_POST["remarks"] ?? "");

$allowedStatuses = [
    "Submitted",
    "Under Review",
    "Assigned",
    "Investigation",
    "Resolved",
    "Closed",
    "Rejected"
];

if ($id <= 0 || !in_array($status, $allowedStatuses, true)) {
    header("Location: ../admin/complaints.php");
    exit;
}

$complaint_sql = "SELECT user_id, complaint_id
                  FROM complaints
                  WHERE id = ?
                  LIMIT 1";

$complaint_stmt = $conn->prepare($complaint_sql);

if (!$complaint_stmt) {
    $_SESSION["admin_complaint_error"] = "Database error.";
    header("Location: ../admin/complaint-details.php?id=" . $id);
    exit;
}

$complaint_stmt->bind_param("i", $id);
$complaint_stmt->execute();

$complaint_result = $complaint_stmt->get_result();

if ($complaint_result->num_rows !== 1) {
    $complaint_stmt->close();
    $conn->close();

    $_SESSION["admin_complaint_error"] = "Complaint not found.";
    header("Location: ../admin/complaints.php");
    exit;
}

$complaint = $complaint_result->fetch_assoc();

$user_id = (int)$complaint["user_id"];
$complaint_number = $complaint["complaint_id"];

$complaint_stmt->close();

$stmt = $conn->prepare(
    "UPDATE complaints
     SET status = ?, updated_at = CURRENT_TIMESTAMP
     WHERE id = ?"
);

if (!$stmt) {
    $_SESSION["admin_complaint_error"] = "Database error.";
    header("Location: ../admin/complaint-details.php?id=" . $id);
    exit;
}

$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {

    $stmt->close();

    $update_sql = "INSERT INTO complaint_updates
                   (complaint_id, status, remarks)
                   VALUES (?, ?, ?)";

    $update_stmt = $conn->prepare($update_sql);

    if ($update_stmt) {

        $update_stmt->bind_param(
            "iss",
            $id,
            $status,
            $remarks
        );

        $update_stmt->execute();
        $update_stmt->close();
    }

    $notification_message =
        "Your complaint " . $complaint_number .
        " status has been updated to " . $status . ".";

    $notification_sql = "INSERT INTO notifications
                         (user_id, complaint_id, message, is_read)
                         VALUES (?, ?, ?, 0)";

    $notification_stmt = $conn->prepare($notification_sql);

    if ($notification_stmt) {

        $notification_stmt->bind_param(
            "iis",
            $user_id,
            $id,
            $notification_message
        );

        $notification_stmt->execute();
        $notification_stmt->close();
    }

    $_SESSION["admin_complaint_success"] =
        "Complaint status updated successfully.";

} else {

    $stmt->close();

    $_SESSION["admin_complaint_error"] =
        "Unable to update complaint status.";
}

$conn->close();

header(
    "Location: ../admin/complaint-details.php?id=" . $id
);

exit;

?>