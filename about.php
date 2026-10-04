<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>About Us - Cyber Crime Reporting Portal</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f7fb;
    color: #1e293b;
}

.navbar {
    background: #0f172a;
    padding: 18px 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    color: white;
    font-size: 20px;
    font-weight: bold;
}

.navbar a {
    color: #cbd5e1;
    text-decoration: none;
    margin-left: 25px;
    font-size: 14px;
}

.navbar a:hover {
    color: white;
}

.container {
    max-width: 1000px;
    margin: 50px auto;
    padding: 20px;
}

.card {
    background: white;
    padding: 35px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

h1 {
    color: #12304d;
    margin-bottom: 15px;
}

h2 {
    color: #12304d;
    margin-bottom: 12px;
}

p {
    color: #64748b;
    line-height: 1.7;
    margin-bottom: 15px;
}

.features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.feature {
    background: #f7f9fc;
    padding: 20px;
    border-radius: 10px;
}

.feature-icon {
    font-size: 30px;
    margin-bottom: 10px;
}

.feature h3 {
    margin-bottom: 8px;
}

@media(max-width: 700px) {

    .navbar {
        padding: 18px 20px;
        flex-direction: column;
        gap: 15px;
    }

    .navbar a {
        margin-left: 10px;
        margin-right: 10px;
    }

    .features {
        grid-template-columns: 1fr;
    }

}

</style>

</head>

<body>

<nav class="navbar">

<div class="logo">
🛡️ Cyber Crime Reporting Portal
</div>

<div>

<a href="index.php">Home</a>
<a href="about.php">About</a>
<a href="faq.php">FAQ</a>
<a href="contact.php">Contact</a>
<a href="login.php">Login</a>

</div>

</nav>


<div class="container">

<div class="card">

<h1>About Cyber Crime Reporting Portal</h1>

<p>
The Cyber Crime Reporting Portal is a web-based platform designed
to provide users with a simple and convenient way to report cyber
crime incidents.
</p>

<p>
The portal allows registered users to submit complaints, provide
incident information and evidence, and track the status of their
complaints.
</p>

<p>
Authorized officers can review assigned complaints, investigate
incidents, update investigation details, and change complaint
statuses.
</p>

<p>
Administrators can manage users, complaints, officers, and other
portal activities through the administration panel.
</p>

</div>


<div class="card">

<h2>Portal Features</h2>

<div class="features">

<div class="feature">

<div class="feature-icon">
📝
</div>

<h3>Report Cyber Crime</h3>

<p>
Submit cyber crime complaints through an online reporting system.
</p>

</div>


<div class="feature">

<div class="feature-icon">
🔍
</div>

<h3>Track Complaints</h3>

<p>
Users can view the current status and progress of their complaints.
</p>

</div>


<div class="feature">

<div class="feature-icon">
📎
</div>

<h3>Upload Evidence</h3>

<p>
Relevant digital evidence can be submitted along with a complaint.
</p>

</div>


<div class="feature">

<div class="feature-icon">
👮
</div>

<h3>Officer Investigation</h3>

<p>
Authorized officers can investigate complaints assigned to them.
</p>

</div>


<div class="feature">

<div class="feature-icon">
🔔
</div>

<h3>Notifications</h3>

<p>
Users can receive notifications related to their complaints.
</p>

</div>


<div class="feature">

<div class="feature-icon">
🔐
</div>

<h3>Secure Access</h3>

<p>
Separate access is provided for users, officers, and administrators.
</p>

</div>

</div>

</div>

</div>

</body>

</html>