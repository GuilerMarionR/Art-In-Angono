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
    <title>ART IN ANGONO - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include '../includes/navigation-admin.php'; ?>

    <!-- Content Text -->
    <div class="background-container">
        <h1 class="heading">WELCOME TO ART IN ANGONO</h1>
        <p class="subheading">
            Learn about our rich cultural heritage through 360-degree virtual tours, interactive exhibits, and specially curated collections that celebrate the genius of Filipino artists. Start your artistic journey now and experience the magic of Angono's unparalleled creativity.
        </p>
    </div>

    <!-- Download Section-->
    <section class="app-download-section">
        <div class="app-content">
            <img src="https://i.imgur.com/gRrh04i.png" alt="Art in Angono App">
            <div class="app-text">
                <h3>Download Now!</h3>
                <p>Experience Angono’s art from anywhere with our app.</p>
                <button>Art in Angono App</button>
            </div>
        </div>
    </section>

    <script src="../js/script.js"></script>
</body>
</html>
