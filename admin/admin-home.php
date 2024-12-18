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
    <title>ART IN ANGONO - Admin Home</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<style>    /* General Styles for the welcome-content */

.background-container {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f5f5f5; /* Optional: Set a background color */
    padding: 50px 20px;
}
/* Welcome Content */
.background-container {
    padding: 30px 15px;
}

.welcome-content {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.welcome-text, .welcome-image {
    flex: 1;
}

.welcome-image img {
    max-width: 55%;
    height: auto;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* App Download Section */
.app-download-section {
    padding: 30px 15px;
    text-align: center;
}

.app-content {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 20px;
}

.app-text {
    max-width: 600px;
}

.app-content img {
    max-width: 100%;
    height: auto;
}

/* Mobile Styles */
@media (max-width: 768px) {
    .navbar .menu {
        display: none;
        flex-direction: column;
        background-color: #C1121F;
        position: absolute;
        top: 60px;
        padding: 10px;
        width: 95%;
    }

    .navbar .menu.active {
        display: flex;
    }

    .hamburger {
        display: flex;
    }

    .welcome-content, .app-content {
        flex-direction: column;
        text-align: center;
    }

    .welcome-image img{
        max-width: 40%;
        margin-right: 30px;
    }
    .app-content img{
    max-width: 30%;
}
.app-download-section{
    max-width: 90%;
}
.app-text{
    font-size: 12px;
}
.welcome-text h1{
    font-size: 20px;
}
.welcome-text p{
    font-size: 15px;
    text-align: left;
    max-width: 60%;
    margin-left: 160px;
}
.logo img{
    width:40px;
    height: 50px;
}
.logo span{
    font-size: 20px;
}
}
</style>
<body>

    <?php include '../includes/navigation-admin.php'; ?>

    <div class="background-container">
    <div class="welcome-content">
        <div class="welcome-text">
            <h1 class="heading">WELCOME TO ART IN ANGONO</h1>
            <p class="subheading">Learn about our rich cultural heritage through 360-degree virtual tours, interactive exhibits, and specially curated collections that celebrate the genius of Filipino artists. Start your artistic journey now and experience the magic of Angono's unparalleled creativity.</p>
        </div>
        <div class="welcome-image">
            <img src="../Assets/index.jpg" alt="Welcome Image">
        </div>
    </div>
</div>


<!-- Download Section-->
<section class="app-download-section">
    <div class="app-content">
        <img src="https://i.imgur.com/gRrh04i.png" alt="Art in Angono App">
        <div class="app-text">
            <h3>Download Now!</h3>
            <p>Discover Angono, the Art Capital of the Philippines, with our ultimate museum guide! 
                Navigate effortlessly using Suggestive Mapping for personalized routes or Manual Mapping to explore at your own pace. Uncover local art, history, and culture with tailored routes, nearby museum alerts, and exclusive insights—all in one app. Dive into Angono's vibrant art scene today!</p>
                <a href="https://art-in-angono.netlify.app/" target="_blank">
            <button>Art in Angono App</button>
            </a>
        </div>
    </div>
</section>

    <script src="../js/script.js"></script>
</body>
</html>
