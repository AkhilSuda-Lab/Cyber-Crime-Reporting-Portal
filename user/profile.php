<?php
require_once "../includes/auth.php";
requireUserLogin();
require_once "../config/database.php";

$user_id = $_SESSION["user_id"];
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $state = trim($_POST["state"] ?? "");

    if ($full_name === "" || $mobile === "") {
        $error = "Name and mobile number are required.";
    } else {
        $stmt = $conn->prepare(
            "UPDATE users
             SET full_name = ?, mobile = ?, address = ?, city = ?, state = ?
             WHERE id = ?"
        );

        if ($stmt) {
            $stmt->bind_param(
                "sssssi",
                $full_name,
                $mobile,
                $address,
                $city,
                $state,
                $user_id
            );

            if ($stmt->execute()) {
                $_SESSION["user_name"] = $full_name;
                $message = "Profile updated successfully.";
            } else {
                $error = "Unable to update profile.";
            }

            $stmt->close();
        } else {
            $error = "Database query failed.";
        }
    }
}

$stmt = $conn->prepare(
    "SELECT full_name, email, mobile, address, city, state
     FROM users
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();

if (!$user) {
    die("User profile not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile</title>

<link rel="stylesheet" href="../assets/css/dashboard.css">

<style>
.profile-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
}

.profile-card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
}

.profile-card h1 {
    color: #12304d;
    margin-top: 0;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 7px;
    font-weight: 600;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 11px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
}

.form-group input[readonly] {
    background: #f1f3f5;
}

.update-btn {
    background: #1976d2;
    color: #fff;
    border: none;
    padding: 12px 22px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}

.success-message {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.error-message {
    background: #ffebee;
    color: #c62828;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.back-link {
    display: inline-block;
    margin-bottom: 20px;
    color: #1976d2;
    text-decoration: none;
    font-weight: 600;
}
</style>
</head>

<body>

<div class="profile-container">

<a href="dashboard.php" class="back-link">
← Back to Dashboard
</a>

<div class="profile-card">

<h1>My Profile</h1>

<?php if ($message): ?>
<div class="success-message">
<?php echo htmlspecialchars($message); ?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="error-message">
<?php echo htmlspecialchars($error); ?>
</div>
<?php endif; ?>

<form method="POST">

<div class="form-group">
<label for="full_name">Full Name</label>

<input
type="text"
id="full_name"
name="full_name"
value="<?php echo htmlspecialchars($user["full_name"]); ?>"
required
>
</div>

<div class="form-group">
<label for="email">Email Address</label>

<input
type="email"
id="email"
value="<?php echo htmlspecialchars($user["email"]); ?>"
readonly
>
</div>

<div class="form-group">
<label for="mobile">Mobile Number</label>

<input
type="text"
id="mobile"
name="mobile"
value="<?php echo htmlspecialchars($user["mobile"]); ?>"
required
>
</div>

<div class="form-group">
<label for="address">Address</label>

<textarea
id="address"
name="address"
rows="4"
><?php echo htmlspecialchars($user["address"] ?? ""); ?></textarea>
</div>

<div class="form-group">
<label for="city">City</label>

<input
type="text"
id="city"
name="city"
value="<?php echo htmlspecialchars($user["city"] ?? ""); ?>"
>
</div>

<div class="form-group">
<label for="state">State</label>

<input
type="text"
id="state"
name="state"
value="<?php echo htmlspecialchars($user["state"] ?? ""); ?>"
>
</div>

<button type="submit" class="update-btn">
Update Profile
</button>

</form>

</div>

</div>

</body>
</html>