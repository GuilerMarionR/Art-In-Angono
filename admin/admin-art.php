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
    <title>Manage Artworks</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="museum-background"></div>
<?php include '../includes/navigation-admin.php' ;?>

    <!-- Banner Section -->
    <div class="banner">
        <div class="book">
            <h1>MANAGE ARTWORKS</h1>
            <a>Manage your artwork details with the buttons below.</p>
            <div class="button-stack">
                <button type="button" onclick="window.location.href='../admin/admin-art-view.php'">All Artworks</button>
                <button type="button" onclick="window.location.href='admin-my-art.php'">My Artworks</button>
            </div>
        </div>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>
