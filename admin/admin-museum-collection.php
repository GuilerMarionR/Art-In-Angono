<?php
session_start();
include '../includes/db_connections.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../logins/login.php");
    exit();
}

$museumName = $_SESSION['username'];
$uploadSuccess = false;
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $originalImagePath = '../uploads/' . basename($_FILES['image']['name']);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $originalImagePath)) {
        $watermarkedImagePath = '../uploads/collections/watermarked_' . basename($_FILES['image']['name']);
        addWatermark($originalImagePath, $watermarkedImagePath, $museumName);

        $stmt = $conn->prepare("INSERT INTO collections (museumName, title, description, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $museumName, $_POST['title'], $_POST['image-description'], $watermarkedImagePath);

        if ($stmt->execute()) {
            $uploadSuccess = true;
        } else {
            $errorMessage = "Error saving data to the database.";
        }
        $stmt->close();
    } else {
        $errorMessage = "Error uploading the file.";
    }
}

function addWatermark($sourceImagePath, $watermarkedImagePath, $text) {
    $image = imagecreatefromstring(file_get_contents($sourceImagePath));
    $fontSize = 20;
    $color = imagecolorallocate($image, 255, 255, 255);
    $fontPath = '../fonts/arial.ttf';
    $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
    $x = imagesx($image) - ($bbox[2] - $bbox[0]) - 10;
    $y = imagesy($image) - ($bbox[1] - $bbox[7]) - 10;
    imagettftext($image, $fontSize, 0, $x, $y, $color, $fontPath, $text);
    imagejpeg($image, $watermarkedImagePath);
    imagedestroy($image);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Collection Image</title>
    <link rel="stylesheet" href="../css/admin-museum-info.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .custom-file-input {
            display: none; /* Hide the original file input */
        }

        .custom-button {
            background-color: #28a745; /* Bootstrap success green */
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            display: inline-block;
            width: 100%; /* Make it take the full width */
            text-align: center;
            font-size: 16px; /* Match font size */
            height: 45px; /* Match the height of the other buttons */
        }

        .custom-button:hover {
            background-color: #218838; /* Darker green on hover */
        }

        .image-preview {
            margin-top: 10px;
            max-width: 300px; /* Set max width for the image preview */
            height: auto;
            border-radius: 5px;
            display: block; /* Ensure the image is treated as a block element for centering */
            margin-left: auto; /* Center the image */
            margin-right: auto; /* Center the image */
        }

        .choose-file-btn {
            background-color: #28a745; /* Green background */
            color: white; /* White text color */
            border: none;
            padding: 10px 20px; /* Padding for the button */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Change cursor to pointer */
            transition: background-color 0.3s; /* Transition effect */
        }
        .choose-file-btn:hover {
            background-color: #218838; /* Darker green on hover */
        }
    </style>
</head>
<body>
<div class="museum-background"></div>
<?php include '../includes/navigation-admin.php'; ?>

<div class="container my-5">
    <h1 class="text-center">Upload Collection Image</h1>
    
    <?php if ($uploadSuccess): ?>
        <div class="alert alert-success">Image uploaded successfully!</div>
    <?php elseif ($errorMessage): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-light p-4 rounded shadow-sm">
        <div class="form-group">
            <label for="image"><i class="fas fa-image"></i> Choose Image:</label>
            <div class="input-group mb-3">
                <input type="file" name="image" id="image" accept="image/*" required class="custom-file-input" onchange="previewImage(event)">
                <label class="choose-file-btn" for="image">Choose File</label>
            </div>
            <small class="form-text text-muted">Accepted formats: JPG, PNG, GIF</small>
        </div>

        <div class="form-group">
            <img id="imagePreview" class="image-preview" alt="Image Preview" style="display: none;">
        </div>

        <div class="form-group">
            <label for="title"><i class="fas fa-pencil-alt"></i> Title:</label>
            <input type="text" name="title" id="title" placeholder="Title" required class="form-control">
        </div>
        <div class="form-group">
            <label for="image-description"><i class="fas fa-align-left"></i> Description:</label>
            <textarea name="image-description" id="image-description" placeholder="Add Description" required class="form-control"></textarea>
        </div>
        <div class="form-actions d-flex justify-content-between">
            <a href="admin-collection.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-danger">Upload</button>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        const imagePreview = document.getElementById('imagePreview');
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function() {
            imagePreview.src = reader.result;
            imagePreview.style.display = 'block'; // Show the image preview
        }

        if (file) {
            reader.readAsDataURL(file); // Read the file as a data URL
        } else {
            imagePreview.src = ''; // Clear the preview if no file is selected
            imagePreview.style.display = 'none'; // Hide the preview
        }
    }
</script>

</body>
</html>
