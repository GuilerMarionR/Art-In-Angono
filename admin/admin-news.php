<?php
session_start(); // Start the session

// Include your database connection
include '../includes/db_connections.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to the login page
    header("Location: ../logins/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="museum-background"></div>
    <?php include '../includes/navigation-admin.php'; ?>

    <!-- Main Section -->
    <div class="banner">
        <div class="book">
            <h1>MANAGE NEWS & EVENTS</h1>
            <p>Use the buttons below to manage news and events.</p>
            <div class="button-stack">
                <button type="button" onclick="window.location.href='admin-events.php'">All News & Events</button>
                <button type="button" onclick="window.location.href='admin-my-news.php'">My News & Events</button>
            </div>
        </div>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>
