<?php
session_start();

// Include database connection
include '../includes/db_connections.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../logins/login.php");
    exit();
}

// Retrieve the title from the URL or form submission
$title = isset($_GET['title']) ? $_GET['title'] : (isset($_POST['title']) ? $_POST['title'] : null);

if (!$title) {
    echo "Invalid title.";
    exit();
}

// Fetch the collection details if it's a GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT title, description, image_url FROM collections WHERE title = ?");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $result = $stmt->get_result();
    $collection = $result->fetch_assoc();
    $stmt->close();
}

// Handle the update on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTitle = !empty($_POST['title']) ? $_POST['title'] : $collection['title'];
    $newDescription = !empty($_POST['description']) ? $_POST['description'] : $collection['description'];

    // Handle image upload only if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $newImageName = uniqid() . "." . $imageFileType;
        $targetDirectory = "../uploads/collections/";
        $targetFile = $targetDirectory . $newImageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $newImageUrl = $targetFile;
        } else {
            echo "Error uploading the image.";
            exit();
        }
    } else {
        $newImageUrl = $collection['image_url'];  // Keep the existing image if no new one is uploaded
    }

    // Update query using title as the identifier
    $updateStmt = $conn->prepare("UPDATE collections SET title = ?, description = ?, image_url = ? WHERE title = ?");
    $updateStmt->bind_param("ssss", $newTitle, $newDescription, $newImageUrl, $title);

    if ($updateStmt->execute()) {
        echo "<script>alert('Collection updated successfully.'); window.location.href = 'admin-collection.php';</script>";
    } else {
        echo "<script>alert('Error updating collection: " . $conn->error . "');</script>";
    }
    $updateStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Collection</title>
    <link rel="stylesheet" href="../css/admin-collection.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .image-preview {
            margin-top: 10px;
            max-width: 150px; /* Set a larger max width for the image preview */
            height: auto;
            border-radius: 5px;
            display: block; /* Ensure the image is treated as a block element for centering */
            margin-left: auto; /* Center the image */
            margin-right: auto; /* Center the image */
        }
        .form-actions {
            display: flex;
            justify-content: space-between;
        }
        .custom-file-input {
            display: none; /* Hide the default file input */
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
        .container {
            max-width: 800px; /* Set a larger max-width for the container */
            margin: 0 auto; /* Center the container */
        }
    </style>
</head>

<body>
<div class="museum-background"></div>
<?php include '../includes/navigation-admin.php'; ?>

<div class="container my-5">
    <h1 class="text-center mb-4">Edit Collection</h1>
    <div class="text-center mb-4">
        <p>Current Image:</p>
        <img src="<?php echo htmlspecialchars($collection['image_url']); ?>" alt="Current Image" class="img-fluid" style="max-width: 300px; display: block; margin-left: auto; margin-right: auto;">
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="bg-light p-4 rounded shadow-sm">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($collection['title']); ?>" required class="form-control">
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" required class="form-control"><?php echo htmlspecialchars($collection['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="image">Image:</label>
            <div>
                <button type="button" class="choose-file-btn" onclick="document.getElementById('image').click();">Choose Image</button>
                <input type="file" name="image" id="image" accept="image/*" class="custom-file-input" onchange="previewImage(event)">
            </div>
            <img id="imagePreview" class="image-preview" alt="Image Preview" style="display: none;">
        </div>
       
        <div class="form-actions">
            <a href="admin-museums.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-danger">Update Collection</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
