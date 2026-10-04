<?php

require_once "../includes/auth.php";
requireUserLogin();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../report-crime.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$category = trim($_POST["category"] ?? "");
$incident_date = $_POST["incident_date"] ?? "";
$incident_time = $_POST["incident_time"] ?? "";
$location = trim($_POST["location"] ?? "");
$description = trim($_POST["description"] ?? "");
$amount = $_POST["amount"] ?? 0;
$suspect_name = trim($_POST["suspect_name"] ?? "");
$suspect_mobile = trim($_POST["suspect_mobile"] ?? "");
$suspect_email = trim($_POST["suspect_email"] ?? "");
$suspect_website = trim($_POST["suspect_website"] ?? "");
$suspect_social_media = trim($_POST["suspect_social_media"] ?? "");

if ($category === "" || $incident_date === "" || $description === "") {
    die("Please fill all required fields.");
}

$complaint_id = "CCR" . date("YmdHis") . rand(100, 999);

$sql = "INSERT INTO complaints
(
    complaint_id,
    user_id,
    category,
    incident_date,
    incident_time,
    location,
    description,
    amount,
    suspect_name,
    suspect_mobile,
    suspect_email,
    suspect_website,
    suspect_social_media
)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param(
    "sisssssdsssss",
    $complaint_id,
    $user_id,
    $category,
    $incident_date,
    $incident_time,
    $location,
    $description,
    $amount,
    $suspect_name,
    $suspect_mobile,
    $suspect_email,
    $suspect_website,
    $suspect_social_media
);

if (!$stmt->execute()) {
    die("Complaint insertion failed: " . $stmt->error);
}

$complaint_db_id = $conn->insert_id;

$stmt->close();

$status_sql = "UPDATE complaints
               SET status = 'Submitted'
               WHERE id = ?";

$status_stmt = $conn->prepare($status_sql);

if ($status_stmt) {
    $status_stmt->bind_param("i", $complaint_db_id);
    $status_stmt->execute();
    $status_stmt->close();
}

$update_sql = "INSERT INTO complaint_updates
(
    complaint_id,
    status,
    remarks
)
VALUES (?, ?, ?)";

$update_stmt = $conn->prepare($update_sql);

if ($update_stmt) {

    $initial_status = "Submitted";
    $initial_remarks = "Complaint has been submitted successfully and is awaiting review.";

    $update_stmt->bind_param(
        "iss",
        $complaint_db_id,
        $initial_status,
        $initial_remarks
    );

    $update_stmt->execute();
    $update_stmt->close();
}

$notification_sql = "INSERT INTO notifications
(
    user_id,
    complaint_id,
    message,
    is_read
)
VALUES (?, ?, ?, 0)";

$notification_stmt = $conn->prepare($notification_sql);

if ($notification_stmt) {

    $notification_message =
        "Your complaint has been received and is under review.";

    $notification_stmt->bind_param(
        "iis",
        $user_id,
        $complaint_db_id,
        $notification_message
    );

    $notification_stmt->execute();
    $notification_stmt->close();
}

if (
    isset($_FILES["evidence"]) &&
    $_FILES["evidence"]["error"] === UPLOAD_ERR_OK
) {

    $upload_directory = "../uploads/evidence/";

    if (!is_dir($upload_directory)) {
        mkdir($upload_directory, 0777, true);
    }

    $original_name = $_FILES["evidence"]["name"];
    $temporary_file = $_FILES["evidence"]["tmp_name"];
    $file_size = $_FILES["evidence"]["size"];

    $file_extension = strtolower(
        pathinfo($original_name, PATHINFO_EXTENSION)
    );

    $allowed_extensions = [
        "jpg",
        "jpeg",
        "png",
        "pdf",
        "doc",
        "docx"
    ];

    if (in_array($file_extension, $allowed_extensions, true)) {

        $new_file_name =
            $complaint_id . "_" . time() . "." . $file_extension;

        $file_path =
            $upload_directory . $new_file_name;

        if (move_uploaded_file($temporary_file, $file_path)) {

            $database_file_path =
                "uploads/evidence/" . $new_file_name;

            $file_type =
                $_FILES["evidence"]["type"];

            $evidence_sql = "INSERT INTO evidence
            (
                complaint_id,
                file_name,
                file_path,
                file_type,
                file_size
            )
            VALUES (?, ?, ?, ?, ?)";

            $evidence_stmt =
                $conn->prepare($evidence_sql);

            if ($evidence_stmt) {

                $evidence_stmt->bind_param(
                    "isssi",
                    $complaint_db_id,
                    $original_name,
                    $database_file_path,
                    $file_type,
                    $file_size
                );

                $evidence_stmt->execute();
                $evidence_stmt->close();
            }
        }
    }
}

$conn->close();

$_SESSION["complaint_success"] = $complaint_id;

header("Location: ../user/my-complaints.php");
exit();

?>