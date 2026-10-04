<?php

$pageTitle = "Cyber Crime Reporting Portal";
require_once "config/database.php";
require_once "includes/functions.php";
include "includes/header.php";
include "includes/navbar.php";
?>

<main>

    <section class="hero">

        <div class="hero-container">
        <div class="hero-content">

            <span class="hero-badge">CYBER CRIME REPORTING PORTAL</span>

                <h1>
                    Report Cyber Crime.
                    <span>Protect Yourself.</span>
                </h1>

                <p>
                    Report cybercrime incidents through a simple,
                    secure and organized reporting process.
                    Submit your complaint, provide supporting evidence,
                    and track its progress.
                </p>

        <div class="hero-buttons">

                <a href="report-crime.php"class="btn btn-primary">Report a Cyber Crime</a>

        </div>
        </div>

            <div class="hero-card">
            <div class="shield-icon">🛡️</div>

            <h2>Stay Cyber Safe</h2>

            <p>Be alert when sharing personal information,financial details and passwords online.</p>

                <div class="security-item">
                    <span>✓</span>
                    Protect your passwords
                </div>

                <div class="security-item">
                    <span>✓</span>
                    Verify suspicious links
                </div>

                <div class="security-item">
                    <span>✓</span>
                    Keep digital evidence
                </div>
            </div>
        </div>
    </section>

    <section class="quick-actions">
        <div class="section-container">

        <div class="section-heading">
            <span>QUICK ACTIONS</span>
            <h2>What would you like to do?</h2>
            <p>Choose an option below to get started.</p>
            </div>

        <div class="action-grid">

                <a href="report-crime.php"class="action-card">
                    <div class="action-icon"> </div>
                    <h3> Report Cyber Crime</h3>
                    <p>Submit a cybercrime complaint with the relevant incident details.</p>
                    <span class="action-link">Report Now →</span>
                </a>

                
                <a href="faq.php"class="action-card">
                    <div class="action-icon"></div>
                    <h3>Frequently Asked Questions</h3>
                    <p>Find answers to common questions about the reporting process.</p>
                    <span class="action-link">View FAQ →</span>
                </a>

            </div>
        </div>
    </section>

    <section class="features-section">

        <div class="section-container">
            <div class="section-heading">
                <span>PORTAL FEATURES</span>
                <h2>Simple, Organized & Secure</h2>
                <p>The portal is designed to make cybercrime reporting easier for users.</p>
            </div>

        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon"> </div>
                    <h3> Secure Authentication</h3>
                    <p>User accounts are protected using secure authentication practic</p>
                </div>

        <div class="feature-card">
            <div class="feature-icon"></div>
                <h3> Easy Reporting</h3>
                <p> Submit important incident information through a structured complaint form.</p>
                </div>

        <div class="feature-card">
            <div class="feature-icon"></div>
                <h3>Evidence Upload</h3>
                <p> Supporting digital evidence can beattached to a complaint.</p>
            </div>

        <div class="feature-card">
            <div class="feature-icon"></div>
                <h3>Complaint Tracking</h3>
                    <p>Users can monitor complaint statusand receive updates.</p>
            </div>
        </div>
        </div>
    </section>

    <section class="how-section">
        <div class="section-container">
            <div class="section-heading">
                <span>HOW IT WORKS</span>
                <h2>Four Simple Steps</h2>
            </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">01</div>
                    <h3>Register</h3>
                    <p>Create your account using your basic information.</p>
                </div>

        <div class="step-card">
            <div class="step-number">2</div>
                <h3>Report</h3>
                    <p>Provide details about the cybercrime incident.</p>
            </div>

        <div class="step-card">
            <div class="step-number">03</div>
                <h3>Track</h3>
                <p>Use your complaint reference to follow its status.</p>
        </div>

        <div class="step-card">
            <div class="step-number">04</div>
                <h3>Receive Updates</h3>
                <p>View updates and notifications related to your complaint.</p>
            </div>
        </div>
        </div>

    </section>

    <section class="notice-section">
        <div class="notice-container">
            <div class="notice-icon"></div>
            <div>
                <h2>Important Safety Notice</h2>
                <p> 
                    Do not share passwords, OTPs, banking PINs,or other sensitive credentials with anyone.
                    Preserve relevant digital evidence when reporting an incident.
                </p>

            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="cta-container">
            <h2>Need to report a cybercrime?</h2>
            <p>Start your complaint and keep your complaint reference number safe.</p>
            <a href="report-crime.php"class="btn btn-primary">Start a Report</a>
        </div>
    </section>
</main>

<?php
include "includes/footer.php";
?>