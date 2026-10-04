<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["officer_id"])) {
    header("Location: ../officer/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../officer/complaints.php");
    exit;
}

$officerId = (int)$_SESSION["officer_id"];
$complaintId = (int)($_POST["complaint_id"] ?? 0);
$status = trim($_POST["status"] ?? "");
$remarks = trim($_POST["investigation_remarks"] ?? "");

$allowedStatuses = [
    "Investigation",
    "Resolved",
    "Closed"
];

if (
    $complaintId <= 0 ||
    !in_array($status, $allowedStatuses, true)
) {
    $_SESSION["officer_error"] =
        "Invalid complaint update.";

    header("Location: ../officer/complaints.php");
    exit;
}

$complaint_sql = "SELECT user_id, complaint_id
                  FROM complaints
                  WHERE id = ?
                  AND assigned_officer_id = ?
                  LIMIT 1";

$complaint_stmt = $conn->prepare($complaint_sql);

if (!$complaint_stmt) {
    $_SESSION["officer_error"] =
        "Database error.";

    header("Location: ../officer/complaints.php");
    exit;
}

$complaint_stmt->bind_param(
    "ii",
    $complaintId,
    $officerId
);

$complaint_stmt->execute();

$complaint_result =
    $complaint_stmt->get_result();

if ($complaint_result->num_rows !== 1) {

    $complaint_stmt->close();
    $conn->close();

    $_SESSION["officer_error"] =
        "Complaint not found or not assigned to you.";

    header("Location: ../officer/complaints.php");
    exit;
}

$complaint =
    $complaint_result->fetch_assoc();

$user_id = (int)$complaint["user_id"];
$complaint_number = $complaint["complaint_id"];

$complaint_stmt->close();

$stmt = $conn->prepare(
    "UPDATE complaints
     SET status = ?,
         investigation_remarks = ?,
         updated_at = CURRENT_TIMESTAMP
     WHERE id = ?
     AND assigned_officer_id = ?"
);

if (!$stmt) {

    $_SESSION["officer_error"] =
        "Database error.";

    header(
        "Location: ../officer/complaints.php"
    );

    exit;
}

$stmt->bind_param(
    "ssii",
    $status,
    $remarks,
    $complaintId,
    $officerId
);

if ($stmt->execute()) {

    $stmt->close();

    $update_sql = "INSERT INTO complaint_updates
                   (complaint_id, status, remarks)
                   VALUES (?, ?, ?)";

    $update_stmt =
        $conn->prepare($update_sql);

    if ($update_stmt) {

        $update_stmt->bind_param(
            "iss",
            $complaintId,
            $status,
            $remarks
        );

        $update_stmt->execute();
        $update_stmt->close();
    }

    $notification_message =
        "Your complaint " .
        $complaint_number .
        " status has been updated to " .
        $status . ".";

    if ($remarks !== "") {

        $notification_message .=
            " Remarks: " . $remarks;
    }

    $notification_sql =
        "INSERT INTO notifications
        (user_id, complaint_id, message, is_read)
        VALUES (?, ?, ?, 0)";

    $notification_stmt =
        $conn->prepare($notification_sql);

    if ($notification_stmt) {

        $notification_stmt->bind_param(
            "iis",
            $user_id,
            $complaintId,
            $notification_message
        );

        $notification_stmt->execute();
        $notification_stmt->close();
    }

    $_SESSION["officer_success"] =
        "Complaint investigation updated successfully.";

} else {

    $stmt->close();

    $_SESSION["officer_error"] =
        "Unable to update complaint.";
}

$conn->close();

header(
    "Location: ../officer/complaint-details.php?id=" .
    $complaintId
);

exit;

?>