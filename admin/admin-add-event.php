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

// Automatically set museumName from the session username
$museumName = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <link rel="stylesheet" href="../css/admin-add-event.css">
    <!-- Ionicons library for icons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #ff0000, #ffffff); /* Red to white gradient */
            margin: 0;
            padding: 20px;
        }

        .artwork-edit-layout {
            max-width: 800px; /* Increased max width */
            margin: 40px auto; /* Adjusted margin for vertical centering */
            padding: 40px; /* Increased padding for larger form */
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px; /* Margin for spacing */
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }

        input[type="text"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .upload-button {
            background-color: green; /* Green background for upload button */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            margin-top: 10px;
        }

        .upload-button:hover {
            opacity: 0.9;
        }

        .save-button, .close-button {
            background-color: red; /* Solid red background for save button */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px; /* Adjusting margin for alignment */
            text-decoration: none; /* No underline for links */
            display: inline-block; /* Allow margin to work */
        }

        .close-button {
            background-color: grey; /* Grey background for close button */
        }

        .save-button:hover, .close-button:hover {
            opacity: 0.9; /* Slightly transparent on hover */
        }

        .button-container {
            text-align: center; /* Center the button container */
            margin-top: 20px; /* Add margin for spacing */
        }

        .current-image {
            margin-top: 10px;
            text-align: center; /* Center align the image */
        }

        .current-image img {
            max-width: 300px; /* Limit the size of the preview image */
            margin-top: 10px; /* Add margin to the top of the image */
        }
    </style>
</head>
<body>

<?php include '../includes/navigation-admin.php'; ?>
<div class="museum-background"></div>
<div class="artwork-edit-layout">
    <h1>Add Event</h1>
    <form action="admin-add-event.php" method="POST" enctype="multipart/form-data">
        <!-- Title Input -->
        <label for="title">Event Title:</label>
        <input type="text" id="title" name="title" required>

        <!-- Museum Name Input (auto-filled) -->
        <label for="museumName">Museum Name:</label>
        <input type="text" id="museumName" name="museumName" value="<?php echo $museumName; ?>" readonly>

        <!-- Date Input -->
        <label for="date">Event Date:</label>
        <input type="date" id="date" name="date" required>

        <!-- Description Input -->
        <label for="description">Event Description:</label>
        <textarea id="description" name="description" rows="4" required></textarea>

        <!-- Image Upload -->
        <label for="image-upload">Upload Image:</label>
        <input type="file" id="image-upload" name="image" accept="image/*" required style="display: none;" onchange="previewImage(event)">
        <button type="button" class="upload-button" onclick="document.getElementById('image-upload').click();">Choose File</button>

        <div class="current-image" id="preview-container" style="display:none;">
            <h5>Image Preview:</h5>
            <img id="preview" src="" alt="Image Preview">
        </div>

        <div class="button-container">
            <a href="admin-my-news.php" class="close-button">Close</a>
            <button type="submit" class="save-button">Save Event</button>
        </div>
    </form>

    <!-- JavaScript for Image Preview -->
    <script>
        function previewImage(event) {
            const previewContainer = document.getElementById('preview-container');
            const previewImage = document.getElementById('preview');
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block'; // Show preview container
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</div>

<?php
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $museumName = $_POST['museumName'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    // Check if image file is uploaded
    if ($image['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/'; // Set your upload directory
        $uploadFile = $uploadDir . basename($image['name']);

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($image['tmp_name'], $uploadFile)) {
            // Prepare SQL statement to insert the event
            $sql = "INSERT INTO events (title, museumName, date, description, image) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $title, $museumName, $date, $description, $uploadFile);

            if ($stmt->execute()) {
                echo "<script>alert('Event added successfully!');</script>";
            } else {
                echo "<script>alert('Error adding event: " . $stmt->error . "');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('Error uploading image.');</script>";
        }
    } else {
        echo "<script>alert('No image uploaded.');</script>";
    }
}

$conn->close(); // Close the database connection
?>

</body>
</html>
