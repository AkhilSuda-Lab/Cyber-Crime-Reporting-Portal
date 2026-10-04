<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireAdminLogin()
{
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../admin/login.php");
        exit();
    }
}

function getLoggedInAdminId()
{
    return $_SESSION['admin_id'] ?? null;
}

function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']);
}

?>