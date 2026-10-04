<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireUserLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: /Project/Cyber%20Crime%20Reporting%20Portal/login.php");
        exit();
    }
}

function getLoggedInUserId()
{
    return $_SESSION['user_id'] ?? null;
}

function isUserLoggedIn()
{
    return isset($_SESSION['user_id']);
}