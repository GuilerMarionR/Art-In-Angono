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
    <title>Admin - Manage Museums</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="museum-background"></div>
    <?php include '../includes/navigation-admin.php' ;?>


    <main class="content" id="image-section">
        <div class="overlay"></div>
        <div class="text-section" id="text-section">
            <div class="title" id="event-title"></div>
            <div class="details" id="event-details"></div>
        </div>
    </main>

    <section class="description" id="event-description"></section>

    <button class="back-button" id="backButton">Back</button> <!-- Back button to return to the all events page -->

    <footer class="footer"></footer>

    <script src="../js/script.js"></script> <!-- Link to your external JavaScript file -->
</body>
</html>
