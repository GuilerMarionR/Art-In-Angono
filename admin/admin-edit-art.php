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

// Check if the artwork ID is provided
if (isset($_GET['id'])) {
    $artworkID = htmlspecialchars($_GET['id']);
    
    // Fetch the existing artwork details
    $query = "SELECT * FROM artworks WHERE artworkID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $artworkID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // Check if the artwork exists
    if ($row = mysqli_fetch_assoc($result)) {
        // Fetch existing values safely
        $title = htmlspecialchars($row['title']);
        $gallery = htmlspecialchars($row['museumName']);
        $artist = htmlspecialchars($row['artistName']);
        $description = htmlspecialchars($row['description']);
        $medium = htmlspecialchars($row['medium']);
        $dimensions = htmlspecialchars($row['dimension']);
        $email = htmlspecialchars($row['email']);
        $link = htmlspecialchars($row['websiteLink']);
        $contact = htmlspecialchars($row['contactNumber']);
        $imagePath = $row['imagePath']; // Store the current image path for possible update
    } else {
        // Artwork not found
        echo "<p>Artwork not found.</p>";
        exit();
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
} else {
    // No artwork ID provided
    echo "<p>No artwork ID provided.</p>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare the data for updating and escape special characters
    $title = htmlspecialchars($_POST['title']);
    $gallery = htmlspecialchars($_POST['gallery']);
    $artist = htmlspecialchars($_POST['artist']);
    $description = htmlspecialchars($_POST['description']);
    $medium = htmlspecialchars($_POST['medium']);
    $dimensions = htmlspecialchars($_POST['dimensions']);
    $email = htmlspecialchars($_POST['email']);
    $link = htmlspecialchars($_POST['link']);
    $contact = htmlspecialchars($_POST['contact']);
    
    // Handle image upload if a new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/artworks/"; // Ensure there's a trailing slash
        $imageFileName = basename($_FILES['image']['name']);
        $imagePath = $targetDir . $imageFileName;

        // Check if the upload is successful
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            // File uploaded successfully, proceed to update the database with the new image path
        } else {
            echo "<p>Error uploading image. Please try again.</p>";
            exit();
        }
    } else {
        // No new image uploaded; keep the old image path
        $imagePath = htmlspecialchars($row['imagePath']); // Use the existing image path
    }

    // Update the artwork in the database
    $updateQuery = "UPDATE artworks SET title = ?, museumName = ?, artistName = ?, imagePath = ?, description = ?, medium = ?, dimension = ?, email = ?, websiteLink = ?, contactNumber = ? WHERE artworkID = ?";
    $updateStmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, 'sssssssssss', $title, $gallery, $artist, $imagePath, $description, $medium, $dimensions, $email, $link, $contact, $artworkID);
    
    if (mysqli_stmt_execute($updateStmt)) {
        echo "<p>Artwork updated successfully!</p>";
        // Optionally, redirect after successful update
        // header("Location: admin-my-art.php");
        // exit();
    } else {
        echo "<p>Error updating artwork: " . mysqli_stmt_error($updateStmt) . "</p>";
    }

    // Close the prepared statement
    mysqli_stmt_close($updateStmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Artwork</title>
    <link rel="stylesheet" href="../css/admin-my-art.css">
    <style>
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px; /* Adjusting margin for alignment */
            color: white;
            text-decoration: none; /* No underline for links */
            display: inline-block; /* Allow margin to work */
        }

        .save {
            background-color: red; /* Red background for save button */
        }

        .cancel {
            background-color: gray; /* Gray background for cancel button */
        }

        .btn:hover {
            opacity: 0.9; /* Slightly transparent on hover */
        }

        .current-image {
            margin-top: 20px;
            border: 1px solid #ccc; /* Optional styling */
            padding: 10px; /* Optional styling */
            text-align: center; /* Centering the image */
        }

        .current-image img {
            max-width: 300px; /* Responsive image */
            height: auto; /* Maintain aspect ratio */
        }
        @media screen and (max-width: 480px) {
            .current-image img {
            max-width: 200px; /* Responsive image */
            height: auto; /* Maintain aspect ratio */
        }
}
    </style>
</head>
<body>
<div class="museum-background"></div>
<?php include '../includes/navigation-admin.php'; ?>

<div class="edit-background">
    <div class="artwork-edit-layout">
        <h1>Edit Artwork</h1>
        <div class="current-image">
                <h3>Current Image</h3>
                <img src="<?php echo $imagePath; ?>" alt="Current Artwork" />
            </div>


        <form id="edit-artwork-form" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?php echo $title; ?>" required>
            </div>
            <div class="form-group">
                <label for="gallery">Gallery</label>
                <input type="text" id="gallery" name="gallery" value="<?php echo $gallery; ?>" required>
            </div>
            <div class="form-group">
                <label for="artist">Artist</label>
                <input type="text" id="artist" name="artist" value="<?php echo $artist; ?>" required>
            </div>
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" id="image" name="image" accept="image/*"> <!-- Limit to image types -->
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?php echo $description; ?></textarea>
            </div>
            <div class="form-group">
                <label for="medium">Medium</label>
                <input type="text" id="medium" name="medium" value="<?php echo $medium; ?>" required>
            </div>
            <div class="form-group">
                <label for="dimensions">Dimensions</label>
                <input type="text" id="dimensions" name="dimensions" value="<?php echo $dimensions; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="form-group">
                <label for="link">Website</label>
                <input type="url" id="link" name="link" value="<?php echo $link; ?>">
            </div>
            <div class="form-group">
                <label for="contact">Contact Number</label>
                <input type="text" id="contact" name="contact" value="<?php echo $contact; ?>" required>
            </div>
            <div class="form-buttons">
                <button type="submit" class="btn save">Save</button>
                <button type="button" class="btn cancel" onclick="window.location.href='admin-my-art.php'">Cancel</button>
            </div>
        </form>
    </div>
</div>
<!-- <script src="../js/admin-edit-art.js"></script> Include your JavaScript file -->
</body>
</html>
