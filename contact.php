<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Contact Us - Cyber Crime Reporting Portal</title>

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

/* Navigation */

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

/* Main */

.container {
    max-width: 1000px;
    margin: 50px auto;
    padding: 20px;
}

.page-title {
    text-align: center;
    margin-bottom: 30px;
}

.page-title h1 {
    color: #12304d;
    margin-bottom: 10px;
}

.page-title p {
    color: #64748b;
}

/* Contact Layout */

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.card h2 {
    color: #12304d;
    margin-bottom: 20px;
}

/* Contact Information */

.contact-item {
    margin-bottom: 20px;
}

.contact-item h3 {
    font-size: 15px;
    color: #334155;
    margin-bottom: 6px;
}

.contact-item p {
    color: #64748b;
    line-height: 1.6;
}

/* Form */

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 7px;
    font-size: 14px;
    font-weight: bold;
    color: #334155;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px;

    border: 1px solid #cbd5e1;
    border-radius: 8px;

    font-size: 14px;
    outline: none;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #2563eb;
}

.form-group textarea {
    min-height: 130px;
    resize: vertical;
}

.submit-btn {
    width: 100%;
    padding: 13px;

    border: none;
    border-radius: 8px;

    background: #2563eb;
    color: white;

    font-size: 15px;
    font-weight: bold;

    cursor: pointer;
}

.submit-btn:hover {
    background: #1d4ed8;
}

/* Note */

.note {
    margin-top: 20px;
    padding: 12px;

    background: #eff6ff;
    border-left: 4px solid #2563eb;

    color: #475569;
    font-size: 13px;
    line-height: 1.5;
}

/* Mobile */

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

    .contact-grid {
        grid-template-columns: 1fr;
    }

    .container {
        margin: 30px auto;
        padding: 15px;
    }

}

</style>

</head>

<body>


<!-- Navigation -->

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


<!-- Main -->

<div class="container">


<div class="page-title">

<h1>Contact Us</h1>

<p>
Have a question or need assistance? Contact the portal support team.
</p>

</div>


<div class="contact-grid">


<!-- Contact Information -->

<div class="card">

<h2>Contact Information</h2>


<div class="contact-item">

<h3>📧 Email</h3>

<p>
support@cybercrimeportal.com
</p>

</div>


<div class="contact-item">

<h3>📞 Phone</h3>

<p>
1800-XXX-XXXX
</p>

</div>


<div class="contact-item">

<h3>📍 Office</h3>

<p>
Cyber Crime Reporting Portal<br>
Cyber Crime Support Centre<br>
India
</p>

</div>


<div class="contact-item">

<h3>🕐 Support Hours</h3>

<p>
Monday – Friday<br>
9:00 AM – 6:00 PM
</p>

</div>


<div class="note">

For emergencies or situations requiring immediate
assistance, contact the appropriate emergency or law
enforcement service.

</div>

</div>


<!-- Contact Form -->

<div class="card">

<h2>Send Us a Message</h2>

<form action="#" method="POST">


<div class="form-group">

<label for="name">
Full Name
</label>

<input
    type="text"
    id="name"
    name="name"
    placeholder="Enter your name"
    required
>

</div>


<div class="form-group">

<label for="email">
Email Address
</label>

<input
    type="email"
    id="email"
    name="email"
    placeholder="Enter your email"
    required
>

</div>


<div class="form-group">

<label for="subject">
Subject
</label>

<input
    type="text"
    id="subject"
    name="subject"
    placeholder="Enter subject"
    required
>

</div>


<div class="form-group">

<label for="message">
Message
</label>

<textarea
    id="message"
    name="message"
    placeholder="Enter your message"
    required
></textarea>

</div>


<button
    type="submit"
    class="submit-btn"
>
Send Message
</button>

</form>

</div>


</div>

</div>

</body>

</html>