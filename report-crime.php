<?php

require_once "includes/auth.php";
requireUserLogin();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Cyber Crime</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Report Cyber Crime</h1>
            <p> Provide accurate information about the incident.</p>
        </div>

        <form action="process/complaint-process.php" method="POST"
            enctype="multipart/form-data">

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category"name="category"required>

                    <option value="">Select crime category</option>
                    <option value="Online Financial Fraud">Online Financial Fraud</option>
                    <option value="Identity Theft">Identity Theft</option>
                    <option value="Cyber Stalking">Cyber Stalking</option>
                    <option value="Hacking">Hacking</option>
                    <option value="Phishing">Phishing</option>
                    <option value="Social Media Crime">Social Media Crime</option>
                    <option value="Cyber Bullying">Cyber Bullying</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="incident_date">Incident Date</label>
                <input type="date"id="incident_date"name="incident_date"required>
            </div>

            <div class="form-group">
                <label for="incident_time">Incident Time</label>
                <input type="time"id="incident_time"name="incident_time">
            </div>

            <div class="form-group">
                <label for="location">Incident Location</label>
                <input type="text"id="location"name="location"
                    placeholder="Enter incident location">
            </div>

            <div class="form-group">
                <label for="description">Incident Description</label>
                <textarea id="description"name="description"rows="6"
                    placeholder="Describe what happened in detail"required></textarea>
            </div>

            <div class="form-group">
                <label for="amount">Financial Loss Amount</label>
                <input type="number" id="amount" name="amount" min="0"
                    step="0.01"placeholder="Enter amount if applicable">
            </div>

            <div class="form-group">
                <label for="suspect_name">Suspect Name</label>
                <input type="text" id="suspect_name" name="suspect_name"
                    placeholder="Enter suspect name if known">
            </div>

            <div class="form-group">
                <label for="suspect_mobile">Suspect Mobile</label>
                <input type="text" id="suspect_mobile" name="suspect_mobile"
                    placeholder="Enter suspect mobile if known">
            </div>

            <div class="form-group">
                <label for="suspect_email">Suspect Email</label>
                <input type="email" id="suspect_email" name="suspect_email"
                    placeholder="Enter suspect email if known">
            </div>

            <div class="form-group">
                <label for="suspect_website">Suspect Website</label>
                <input type="url" id="suspect_website" name="suspect_website"
                    placeholder="Enter website URL if known">
            </div>

            <div class="form-group">
                <label for="suspect_social_media">Suspect Social Media</label>
                <input type="text"id="suspect_social_media" name="suspect_social_media"
                    placeholder="Enter social media profile if known">
            </div>

            <div class="form-group">
                <label for="evidence">Evidence</label>
                <input type="file" id="evidence" name="evidence"
                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
            </div>

            <button type="submit" class="auth-btn">Submit Complaint</button>

        </form>
    </div>

</div>
</body>
</html>