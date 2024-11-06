<?php
session_start(); // Start the session

// Check if the user is logged in
if (isset($_POST['logout_confirm'])) {
    // Unset all session variables and destroy the session
    session_unset();
    session_destroy();

    // Redirect to the login page after logout
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Logout Confirmation</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <!-- Logo Section -->
        <div class="logo">
            <img src="https://i.imgur.com/BeaSiLw.png" alt="Art in Angono Logo" class="logo-img">
            <span>ART IN ANGONO - ADMIN</span>
        </div>

        <!-- Menu Section -->
        <div class="menu">
            <a href="..admin/admin-home.php">HOME</a>
            <a href="..admin/admin-museums.php">MUSEUMS</a>
            <a href="../admin/admin-news.php">NEWS & EVENTS</a>
            <a href="../admin/admin-art.php">ARTWORKS</a>
            <a href="../admin/admin-bookings.php">BOOK A TOUR</a>
            <a href="logout.php" id="logout-btn">LOGOUT</a>
        </div>
    </div>

    <section class="logoutbg">
        <div class="overlay" id="overlay"></div>
        <div class="logout-box" id="logout-box">
            <h3>Are you sure you want to logout?</h3>
            <form method="POST" action=""> <!-- Use POST method to confirm logout -->
                <div class="button-container">
                    <button type="submit" name="logout_confirm" class="btn-yes">Yes</button>
                    <button type="button" class="btn-cancel" onclick="window.location.href='../admin/admin-home.php'">Cancel</button>
                </div>
            </form>
        </div>
    </section>

    <script src="../js/logout.js"></script>
</body>
</html>
