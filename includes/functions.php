<?php

function redirect($url)
{
    header("Location: " . $url);
    exit();
}

function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function generateComplaintID($conn)
{
    do {
        $complaintID = "CCR" . date("Y") . rand(100000, 999999);
        $stmt = $conn->prepare(
            "SELECT id FROM complaints WHERE complaint_id = ?"
        );

        $stmt->bind_param("s", $complaintID);
        $stmt->execute();
        $result = $stmt->get_result();
    } while ($result->num_rows > 0);

    $stmt->close();
    return $complaintID;
}

function setFlashMessage($type, $message)
{
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

function displayFlashMessage()
{
    if (
        isset($_SESSION['flash_message']) &&
        isset($_SESSION['flash_type'])
    ) {
        $type = e($_SESSION['flash_type']);
        $message = e($_SESSION['flash_message']);

        echo '<div class="alert alert-' . $type . '">';
        echo $message;
        echo '</div>';

        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
    }
}

?>