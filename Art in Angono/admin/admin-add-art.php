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

$username = $_SESSION['username']; // Store username for autofill

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'];
    $galleryName = $username; // Automatically set to the admin's username
    $artistName = $_POST['artist'];
    $description = $_POST['description'];
    $medium = $_POST['medium'];
    $dimensions = $_POST['dimensions'];
    $email = $_POST['email'];
    $link = $_POST['link'];
    $contact = $_POST['contact'];

    // Handle image upload
    $imagePath = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "../uploads/";
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            // File successfully uploaded
        } else {
            echo "Error uploading the file.";
        }
    }

    // Insert data into the database
    $query = "INSERT INTO artworks (title, museumName, artistName, description, medium, dimension, email, websiteLink, contactNumber, imagePath) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssssssssss', $title, $galleryName, $artistName, $description, $medium, $dimensions, $email, $link, $contact, $imagePath);

    if (mysqli_stmt_execute($stmt)) {
        echo "<p>Artwork added successfully!</p>";
    } else {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO</title>
    <link rel="stylesheet" href="../css/admin-my-art.css">
    <style>
        .current-image {
            margin-top: 20px;
            border: 1px solid #ccc; /* Optional styling */
            padding: 10px; /* Optional styling */
            text-align: center; /* Centering the image */
        }

        .current-image img {
            max-width: 100%; /* Responsive image */
            height: auto; /* Maintain aspect ratio */
        }
    </style>
</head>
<body>

<?php include '../includes/navigation-admin.php'; ?>
<div class="museum-background"></div>
<div class="button-group">
    <button onclick="window.location.href='admin-my-art.php'">My Artworks</button>
    <button onclick="window.location.href='admin-add-art.php'">Add Artwork</button>
</div>

<div class="artwork-edit-layout">
    <h1>Add New Artwork</h1>
    <form id="artworks" action="" method="POST" enctype="multipart/form-data">
        <!-- Add Title -->
        <div class="form-group">
            <label for="title">Artwork Title</label>
            <input type="text" id="title" name="title" placeholder="Enter artwork title" required>
        </div>

        <!-- Autofilled Gallery Name -->
        <div class="form-group">
            <label for="gallery">Gallery Name</label>
            <input type="text" id="gallery" name="gallery" value="<?php echo htmlspecialchars($username); ?>" readonly>
        </div>

        <!-- Add Artist Name -->
        <div class="form-group">
            <label for="artist">Artist</label>
            <input type="text" id="artist" name="artist" placeholder="Enter artist name" required>
        </div>

        <!-- Add Image -->
        <div class="form-group">
            <label for="image">Artwork Image</label>
            <input type="file" id="image" name="image" accept="image/*" required onchange="previewImage(event)">
        </div>

        <div class="current-image" id="image-preview-container" style="display:none;">
            <h3>Image Preview</h3>
            <img id="image-preview" src="#" alt="Image Preview" />
        </div>

        <!-- Add Description -->
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" placeholder="Enter artwork description" required></textarea>
        </div>

        <!-- Add Medium -->
        <div class="form-group">
            <label for="medium">Medium</label>
            <input type="text" id="medium" name="medium" placeholder="Enter medium used (e.g. Acrylic, Oil, etc.)" required>
        </div>

        <!-- Add Dimensions -->
        <div class="form-group">
            <label for="dimensions">Dimensions</label>
            <input type="text" id="dimensions" name="dimensions" placeholder="Enter dimensions (e.g. 48&quot; x 36&quot;)" required>
        </div>

        <!-- Contact Details (Email, Website, Contact Number) -->
        <div class="form-group">
            <label for="contact-details">Contact Details</label>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <input type="url" id="link" name="link" placeholder="Website Link">
            <input type="tel" id="contact" name="contact" placeholder="Contact Number" required>
        </div>

        <!-- Submit and Cancel buttons -->
        <div class="form-buttons">
            <button type="submit" class="btn save">Add Artwork</button>
            <button type="button" class="btn cancel" onclick="window.location.href='admin-my-art.php'">Cancel</button>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        const imagePreviewContainer = document.getElementById('image-preview-container');
        const imagePreview = document.getElementById('image-preview');

        // Set the source of the image preview
        imagePreview.src = URL.createObjectURL(event.target.files[0]);
        
        // Display the image preview container
        imagePreviewContainer.style.display = 'block';
    }
</script>

</body>
</html>
