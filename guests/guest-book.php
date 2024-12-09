<?php
// Start the session
session_start();

// Check if the user is logged in by checking for the session username
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    // If not logged in, redirect to the login page
    header('Location: ../logins/login.php');
    exit(); // Make sure no further code is executed after the redirect
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - Book</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>

    <?php include '../includes/navigation-loggedin.php'; // Include navbar for logged-in users ?>
    <div class="banner">
    <div class="book">
    <div class="content-container">
        <!-- Tabs for Museum List and Booking History -->
         
        <div class="tabs">
            <button onclick="showTab('museums')">List of Museums</button>
                    <a href="guest-booking.php">
                <button>Booking History</button>
            </a>
        </div>
        <!-- Museum List Tab Content -->
        <div id="museums" class="tab-content">
            <h1>LIST OF MUSEUMS</h1>
            <p>Choose a museum you want to book a tour.</p>
            <div>
                <button type="button" onclick="redirectToTerms('Angono Petroglyphs')" aria-label="Select Angono Petroglyphs">Angono Petroglyphs</button>
                <button type="button" onclick="redirectToTerms('Balagtas Gallerie')" aria-label="Select Balagtas Gallerie">Balagtas Gallerie</button>
                <button type="button" onclick="redirectToTerms('Balaw-Balaw Art Gallerie')" aria-label="Select Balaw-Balaw Art Gallerie">Balaw-Balaw Art Gallerie</button>
                <button type="button" onclick="redirectToTerms('Blanco Family Museum')" aria-label="Select Blanco Family Museum">Blanco Family Museum</button>
                <br>
                <button type="button" onclick="redirectToTerms('Galleria Perlita')" aria-label="Select Galleria Perlita">Galleria Perlita</button>
                <button type="button" onclick="redirectToTerms('Giant Dwarf Art Space')" aria-label="Select Giant Dwarf Art Space">Giant Dwarf Art Space</button>
                <button type="button" onclick="redirectToTerms('House of Botong Francisco')" aria-label="Select House of Botong Francisco">House of Botong Francisco</button>
                <button type="button" onclick="redirectToTerms('Juban Gallery')" aria-label="Select Juban Gallery">Juban Gallery</button>
                <br>
                <button type="button" onclick="redirectToTerms('Kuta Artspace')" aria-label="Select Kuta Artspace">Kuta Artspace</button>
                <button type="button" onclick="redirectToTerms('Nemiranda Arthouse')" aria-label="Select Nemiranda Arthouse">Nemiranda Arthouse</button>
                <button type="button" onclick="redirectToTerms('Nono Museum')" aria-label="Select Nono Museum">Nono Museum</button>
            </div>
        </div>
    <script src="../js/script.js"></script>
</body>
</html>
