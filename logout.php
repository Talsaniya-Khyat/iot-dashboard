<?php
// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header('Location: login.php');
    exit;
} else {
    // User is not logged in, redirect to login page
    header('Location: login.php');
    exit;
}
