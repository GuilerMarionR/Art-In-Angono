<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - Terms & Conditions</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php 
    // Check if the user is logged in by checking for the session username
    if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        // If logged in, include the logged-in navbar
        include '../includes/navigation-loggedin.php';
    } else {
        // If not logged in, include the guest navbar
        include '../includes/navigation-guest.php';
    }
    ?>
    <div class="terms-container">
        <div class="terms-box">
            <div class="terms-text">
                <h2>TERMS AND CONDITIONS</h2>
                <p>By using our website, you agree to comply with and be bound by the following Terms and Conditions. Please read these terms carefully before making any booking. By using this website, you confirm that you are at least 18 years old and capable of entering into a binding contract. Art in Angono reserves the right to modify these terms at any time without prior notice. By continuing to use this site, you agree to abide by any updated terms.

Once you submit a booking request, you will receive an e-receipt, which you should download. Please verify all the details carefully. All bookings are subject to availability and acceptance. You agree to provide accurate information, including personal details, when making a booking.

Art in Angono is not liable for any direct, indirect, incidental, or consequential damages arising from the use of this site or its services. Your use of this website is also governed by our Privacy Policy, which outlines how we collect, use, and protect your information.

These terms and conditions are governed by and construed in accordance with Republic Act 10173, or the Data Privacy Act of 2012 (DPA) of the Republic of the Philippines.</p>
                <p><strong>Selected Museum:</strong> 
                <?php
                // Retrieve the museum parameter from the URL
                if (isset($_GET['museum']) && !empty($_GET['museum'])) {
                    $museum = htmlspecialchars($_GET['museum']); // Use htmlspecialchars to prevent XSS
                } else {
                    $museum = 'Unknown Museum'; // Default value if not set
                }
                echo $museum; // Display the museum name
                ?>
                </p>
                <!-- Add the rest of your terms here -->
            </div>
            <div class="button-container">
                <button class="btn red-btn" onclick="window.location.href='guest-notice.php?museum=<?php echo urlencode($museum); ?>'">AGREE</button>
                <button class="btn gray-btn" onclick="window.location.href='guest-book.php'">CANCEL</button>
            </div>
        </div>
    </div>
    <script src="../js/scripts.js" defer></script>
</body>
</html>
