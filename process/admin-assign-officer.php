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

$complaint_id = (int)($_POST["complaint_id"] ?? 0);
$officer_id = (int)($_POST["officer_id"] ?? 0);

if ($complaint_id <= 0 || $officer_id <= 0) {
    $_SESSION["admin_complaint_error"] = "Please select a valid officer.";
    header("Location: ../admin/complaint-details.php?id=" . $complaint_id);
    exit;
}

$stmt = $conn->prepare(
    "SELECT id
     FROM officers
     WHERE id = ?
     AND status = 'Active'
     LIMIT 1"
);

$stmt->bind_param("i", $officer_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();

    $_SESSION["admin_complaint_error"] = "Selected officer does not exist or is inactive.";
    header("Location: ../admin/complaint-details.php?id=" . $complaint_id);
    exit;
}

$stmt->close();

$stmt = $conn->prepare(
    "UPDATE complaints
     SET assigned_officer_id = ?,
         status = 'Assigned',
         updated_at = CURRENT_TIMESTAMP
     WHERE id = ?"
);

$stmt->bind_param("ii", $officer_id, $complaint_id);

if ($stmt->execute()) {
    $_SESSION["admin_complaint_success"] = "Complaint assigned to officer successfully.";
} else {
    $_SESSION["admin_complaint_error"] = "Unable to assign complaint.";
}

$stmt->close();
$conn->close();

header("Location: ../admin/complaint-details.php?id=" . $complaint_id);
exit;

?>