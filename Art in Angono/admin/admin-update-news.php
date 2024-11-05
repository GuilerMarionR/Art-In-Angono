<?php
session_start();
include '../includes/db_connections.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../logins/login.php");
    exit();
}

$currentUsername = $_SESSION['username'];
$eventID = $_GET['eventID'] ?? null;

if ($eventID) {
    // Fetch current event data
    $sql = "SELECT * FROM events WHERE eventID = ? AND museumName = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $eventID, $currentUsername);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateEvent'])) {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    $newImage = $_FILES['image']['name'] ? $_FILES['image'] : null;

    if ($newImage) {
        $imagePath = '../uploads/events/' . basename($newImage['name']);
        move_uploaded_file($newImage['tmp_name'], $imagePath);

        // Update query with image
        $updateSql = "UPDATE events SET title = ?, date = ?, description = ?, image = ? WHERE eventID = ? AND museumName = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssssss", $title, $date, $description, $imagePath, $eventID, $currentUsername);
    } else {
        // Update query without image
        $updateSql = "UPDATE events SET title = ?, date = ?, description = ? WHERE eventID = ? AND museumName = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sssss", $title, $date, $description, $eventID, $currentUsername);
    }

    if ($stmt->execute()) {
        header("Location: admin-my-news.php"); // Redirect after successful update
        exit();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Event</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to right, #ff0000, #ffffff); /* Red to white gradient */
            padding: 20px;
        }
        .event-details {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 40px; /* Increased padding for larger form */
            max-width: 800px; /* Increased max width */
            margin: 40px auto;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .current-image {
            margin-top: 10px;
            text-align: center; /* Center align the text */
        }
        .current-image img {
            max-width: 100px; /* Limit the size of the image */
            margin-top: 10px; /* Add margin to the top of the image */
        }
        .btn.cancel {
            background-color: grey;
            color: white;
        }
        .btn.cancel:hover {
            opacity: 0.8;
        }
        .btn.save {
            background-color: red;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .btn-file {
            background-color: green; /* Green background for file input button */
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
        }
        .btn-file:hover {
            opacity: 0.8; /* Slightly transparent on hover */
        }
        .file-input {
            display: none; /* Hide the default file input */
        }
    </style>
</head>
<body>
<div class="museum-background"></div>
<?php include '../includes/navigation-admin.php'; ?>

<div class="event-details">
    <h1>Update Event</h1>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <div class="current-image">
                <div>Current Image:</div>
                <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="Event Image">
            </div>
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($event['date']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Update Image:</label>
            <button type="button" class="btn-file" onclick="document.getElementById('image').click();">Choose File</button>
            <input type="file" class="file-input" id="image" name="image" accept="image/*" onchange="previewImage(event)">
            <div class="current-image" id="preview-container" style="display:none;">
                <h5>Image Preview:</h5>
                <img id="preview" src="" alt="Image Preview" style="max-width: 300px; margin-top: 10px;">
            </div>
        </div>
        <div class="form-buttons">
            <a href="admin-my-news.php" class="btn cancel">Cancel</a>
            <button type="submit" name="updateEvent" class="btn save">Update Event</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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

</body>
</html>
