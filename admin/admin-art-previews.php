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

// Fetch artwork ID from URL
if (isset($_GET['id'])) {
    $artworkId = $_GET['id'];

    // Prepare and execute the SQL statement to fetch artwork details
    $query = "SELECT title, museumName, artistName, description, medium, dimension, email, websiteLink, contactNumber, imagePath FROM artworks WHERE artworkID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $artworkId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch artwork details
    if ($artwork = mysqli_fetch_assoc($result)) {
        // Store artwork details in variables
        $title = htmlspecialchars($artwork['title']);
        $museumName = htmlspecialchars($artwork['museumName']);
        $artistName = htmlspecialchars($artwork['artistName']);
        $description = htmlspecialchars($artwork['description']);
        $medium = htmlspecialchars($artwork['medium']);
        $dimensions = htmlspecialchars($artwork['dimension']);
        $email = htmlspecialchars($artwork['email']);
        $websiteLink = htmlspecialchars($artwork['websiteLink']);
        $contactNumber = htmlspecialchars($artwork['contactNumber']);
        $imagePath = htmlspecialchars($artwork['imagePath']);
    } else {
        echo "<p>Artwork not found.</p>";
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<p>No artwork selected.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Link to your CSS file for navigation -->
    <style>
        /* Basic styling for testing */
        body {
            font-family: Arial, sans-serif;
            background-color: grey;
            margin: 0;
            padding: 0;
        }
        
        .artwork-details {
            border: 1px solid #ccc;
            padding: 20px;
            background: whitesmoke;
            margin: 50px auto; /* Center the box */
            max-width: 600px; /* Set a maximum width */
            text-align: center; /* Center text */
            position: relative; /* For positioning the close button */
        }

        .artwork-image {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
            cursor: pointer; /* Indicate click functionality */
        }

        .close-button {
            position: absolute; /* Position the close button */
            top: 10px; 
            right: 10px;
            font-size: 24px; /* Size of the 'X' icon */
            text-decoration: none; /* No underline */
            color: black; /* Color of the 'X' */
        }

        .close-button:hover {
            color: black; /* Change color on hover */
        }

        /* Fullscreen overlay for zoomed image */
        .overlay {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000; /* Ensure it is on top */
        }

        .overlay img {
            max-width: 90%;
            max-height: 90%;
        }

        /* Disable selection */
        * {
            user-select: none;
        }
    </style>
</head>
<body oncontextmenu="return false;"> <!-- Disable right-click -->
<div class="museum-background"></div>
<?php include '../includes/navigation-admin.php'; ?>

<div class="artwork-details">
    <h1>Artwork Details</h1>
    <a href="admin-my-art.php" class="close-button">&times;</a> <!-- Close button -->
    <h1><?php echo $title; ?></h1>
    <p><strong>Gallery:</strong> <?php echo $museumName; ?></p>
    <p><strong>Artist:</strong> <?php echo $artistName; ?></p>
    
    <!-- Artwork Image -->
    <img class="artwork-image" src="<?php echo $imagePath; ?>" alt="Artwork Image" id="artworkImg">
    
    <p><strong>Description:</strong> <?php echo $description; ?></p>
    <p><strong>Medium:</strong> <?php echo $medium; ?></p>
    <p><strong>Dimensions:</strong> <?php echo $dimensions; ?></p>
    <p><strong>Email:</strong> <?php echo $email; ?></p>
    <p><strong>Website:</strong> <a href="<?php echo $websiteLink; ?>" target="_blank"><?php echo $websiteLink; ?></a></p>
    <p><strong>Contact Number:</strong> <?php echo $contactNumber; ?></p>
</div>

<!-- Fullscreen overlay for zoomed image -->
<div class="overlay" id="imageOverlay">
    <img src="" alt="Zoomed Artwork" id="zoomedImg">
</div>

<script>
    // Function to disable keyboard shortcuts for print screen
    document.onkeydown = function(e) {
        if (e.ctrlKey && (e.key === 'p' || e.key === 'c' || e.key === 's')) {
            e.preventDefault();
        }
    };

    // Disable right-click
    document.oncontextmenu = function() {
        return false;
    };

    // Zoom functionality
    const artworkImg = document.getElementById('artworkImg');
    const imageOverlay = document.getElementById('imageOverlay');
    const zoomedImg = document.getElementById('zoomedImg');

    artworkImg.onclick = function() {
        zoomedImg.src = artworkImg.src; // Set the zoomed image source
        imageOverlay.style.display = 'flex'; // Show the overlay
    };

    // Close zoomed image when clicking on the overlay
    imageOverlay.onclick = function() {
        imageOverlay.style.display = 'none'; // Hide the overlay
    };
</script>

</body>
</html>
