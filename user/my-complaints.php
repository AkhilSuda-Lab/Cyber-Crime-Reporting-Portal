<?php

require_once "../includes/auth.php";
requireUserLogin();

require_once "../config/database.php";

$user_id = $_SESSION["user_id"];

$sql = "SELECT
            id,
            complaint_id,
            category,
            incident_date,
            amount,
            status,
            investigation_remarks,
            created_at,
            updated_at
        FROM complaints
        WHERE user_id = ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database query failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>My Complaints | Cyber Crime Portal</title>

<link
    rel="stylesheet"
    href="../assets/css/dashboard.css"
>

<style>

.complaints-container {
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
}

.complaints-container h1 {
    color: #12304d;
    margin-bottom: 10px;
}

.complaints-container > p {
    color: #666;
    margin-bottom: 30px;
}

/* Main table */

.table-container {
    overflow-x: auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1000px;
}

th,
td {
    padding: 14px;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
}

th {
    background: #12304d;
    color: white;
    font-size: 14px;
}

td {
    color: #334155;
    font-size: 14px;
}

tr:hover {
    background: #f5f8fb;
}

/* Status */

.status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: bold;
    white-space: nowrap;
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
    background: #eeeeee;
    color: #555;
}

.rejected {
    background: #ffebee;
    color: #c62828;
}

/* Remarks */

.remarks {
    max-width: 250px;
    line-height: 1.5;
    color: #475569;
}

.no-remarks {
    color: #94a3b8;
    font-style: italic;
}

/* Buttons */

.view-btn {
    display: inline-block;
    padding: 8px 13px;
    background: #1976d2;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: bold;
    white-space: nowrap;
}

.view-btn:hover {
    background: #125ca1;
}

/* No complaints */

.no-complaints {
    padding: 40px;
    text-align: center;
    background: white;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
}

.no-complaints h3 {
    color: #12304d;
    margin-bottom: 10px;
}

.no-complaints p {
    color: #666;
    margin-bottom: 20px;
}

.back-btn {
    display: inline-block;
    margin-bottom: 25px;
    text-decoration: none;
    color: #1976d2;
    font-weight: bold;
}

.back-btn:hover {
    text-decoration: underline;
}

/* Updated time */

.updated-time {
    font-size: 12px;
    color: #64748b;
    line-height: 1.5;
}

</style>

</head>

<body>

<div class="complaints-container">

<a
    href="dashboard.php"
    class="back-btn"
>
    ← Back to Dashboard
</a>

<h1>My Complaints</h1>

<p>
    View and track the cybercrime complaints submitted by you.
</p>

<?php if ($result->num_rows > 0): ?>

<div class="table-container">

<table>

<thead>

<tr>

<th>Complaint ID</th>

<th>Category</th>

<th>Incident Date</th>

<th>Amount</th>

<th>Status</th>

<th>Investigation Remarks</th>

<th>Last Updated</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php while ($complaint = $result->fetch_assoc()): ?>

<?php

$status_class = strtolower(
    str_replace(
        " ",
        "-",
        $complaint["status"]
    )
);

?>

<tr>

<!-- Complaint ID -->

<td>

<strong>
<?php

echo htmlspecialchars(
    $complaint["complaint_id"]
);

?>
</strong>

</td>

<!-- Category -->

<td>

<?php

echo htmlspecialchars(
    $complaint["category"]
);

?>

</td>

<!-- Incident Date -->

<td>

<?php

echo htmlspecialchars(
    $complaint["incident_date"]
);

?>

</td>

<!-- Amount -->

<td>

₹<?php

echo number_format(
    (float)$complaint["amount"],
    2
);

?>

</td>

<!-- Status -->

<td>

<span
    class="status <?php

    echo htmlspecialchars(
        $status_class
    );

    ?>"
>

<?php

echo htmlspecialchars(
    $complaint["status"]
);

?>

</span>

</td>

<!-- Investigation Remarks -->

<td>

<div class="remarks">

<?php if (
    !empty($complaint["investigation_remarks"])
): ?>

<?php

echo nl2br(
    htmlspecialchars(
        $complaint["investigation_remarks"]
    )
);

?>

<?php else: ?>

<span class="no-remarks">
No investigation update yet
</span>

<?php endif; ?>

</div>

</td>

<!-- Last Updated -->

<td>

<div class="updated-time">

<?php

echo htmlspecialchars(
    $complaint["updated_at"]
);

?>

</div>

</td>

<!-- Action -->

<td>

<a
    href="complaint-details.php?id=<?php

    echo (int)$complaint["id"];

    ?>"
    class="view-btn"
>

View Details

</a>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<?php else: ?>

<div class="no-complaints">

<h3>No Complaints Found</h3>

<p>
You have not submitted any cybercrime complaints yet.
</p>

<a
    href="../report-crime.php"
    class="view-btn"
>
Report a Cyber Crime
</a>

</div>

<?php endif; ?>

</div>

</body>

</html>

<?php

$stmt->close();
$conn->close();

?>
