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
        Discover Angono, the Art Capital of the Philippines, with our ultimate museum guide! Navigate effortlessly using Suggestive Mapping for personalized routes or Manual Mapping to explore at your own pace. Uncover local art, history, and culture with tailored routes, nearby museum alerts, and exclusive insights—all in one app. Dive into Angono's vibrant art scene today!</p>
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
