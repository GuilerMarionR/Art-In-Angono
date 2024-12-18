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
<style>
    /* Ensure the terms box fits the page nicely */
.terms-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Full viewport height */
    margin: 0;
}

/* Center the terms content box */
.terms-box {
    width: 90%; /* Make it take 90% of the screen width */
    max-width: 800px; /* Limit the maximum width to 800px */
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    overflow: auto; /* Prevent overflow of content */
}

/* Terms text styling */
.terms-text {
    margin-bottom: 20px;
}

/* Button container */
.button-container {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap; /* Allow buttons to wrap on smaller screens */
    margin-top: 20px;
}

/* Styling for the buttons */
.btn {
    padding: 12px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 48%; /* Buttons will take 48% of the container's width */
}

/* Red button */
.red-btn {
    background-color: #C1121F;
    color: white;
}

/* Gray button */
.gray-btn {
    background-color: #f0f0f0;
    color: #333;
}

/* On hover, add a hover effect */
.btn:hover {
    opacity: 0.8;
}

/* Adjust styles for mobile devices */
@media (max-width: 768px) {
    .terms-box {
        padding: 15px; /* Reduce padding on mobile */
    }

    .btn {
        font-size: 14px; /* Smaller text size for mobile */
        padding: 10px 18px; /* Smaller padding on mobile */
        width: 100%; /* Make buttons full width on mobile */
    }

    .button-container {
        gap: 20px;
    }
}

</style>
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
