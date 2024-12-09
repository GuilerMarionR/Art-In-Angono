<?php
session_start();

// Include database connection
include '../includes/db_connections.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../logins/login.php");
    exit();
}

// Set the museum name based on the logged-in username
$museumName = $_SESSION['username'];

// Fetch featured collections for the specific museum
$stmt = $conn->prepare("SELECT title, description, image_url FROM collections WHERE museumName = ?");
$stmt->bind_param("s", $museumName);
$stmt->execute();
$result = $stmt->get_result();

$collections = [];
while ($row = $result->fetch_assoc()) {
    $collections[] = $row;
}
$stmt->close();

// Handle delete request
if (isset($_POST['delete'])) {
    $titleToDelete = $_POST['title'];
    $deleteStmt = $conn->prepare("DELETE FROM collections WHERE title = ? AND museumName = ?");
    $deleteStmt->bind_param("ss", $titleToDelete, $museumName);
    if ($deleteStmt->execute()) {
        echo "<script>alert('Collection deleted successfully.'); window.location.reload();</script>";
    } else {
        echo "<script>alert('Error deleting collection.');</script>";
    }
    $deleteStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Featured Collections</title>
    <link rel="stylesheet" href="../css/admin-collection.css">
    <style>
        /* Gallery Layout */
        .gallery-container {
            padding: 20px;
            text-align: center;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .gallery-item {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .gallery-item:hover {
            transform: scale(1.05);
        }

        .gallery-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .gallery-info {
            padding: 10px;
            background-color: #f8f8f8;
        }

        /* Modal Styling */
        #imageModal {
            display: none; /* Ensure modal is hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .modal-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            padding: 20px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            overflow: hidden;
        }

        .modal-image-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-image {
            max-height: 80vh;
            max-width: 80vw;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .modal-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2em;
            color: rgba(255, 255, 255, 0.3);
            pointer-events: none;  
            border: 3px solid rgba(255, 255, 255, 0.7); /* Add border */
            border-radius: 5px; /* Optional: for rounded corners */
            background-color: rgba(0, 0, 0, 0.5); /* Optional: add a semi-transparent background */
       
        }

        .modal-info {
            margin-top: 15px;
            font-size: 1.1em;
            line-height: 1.5;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 25px;
            font-weight: bold;
            cursor: pointer;
        }

        .button-container {
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            background-color: #007bff; /* Default button color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #0056b3; /* Darker default on hover */
        }

        .red-button {
            background-color: #dc3545; /* Red */
            color: white;
        }

        .red-button:hover {
            background-color: #c82333; /* Darker red on hover */
        }

        .grey-button {
            background-color: #6c757d; /* Grey */
            color: white;
        }

        .grey-button:hover {
            background-color: #5a6268; /* Darker grey on hover */
        }
    </style>
    <script>
        function openModal(imageSrc, title, description, watermark) {
            const modal = document.getElementById("imageModal");
            modal.style.display = "flex";
            document.getElementById("modalImage").src = imageSrc;
            document.getElementById("modalWatermark").innerText = watermark;
            document.getElementById("modalTitle").innerText = "Title: " + title;
            document.getElementById("modalDescription").innerText = "Description: " + description;

            // Close modal on background click
            modal.onclick = closeModal;
        }

        function closeModal() {
            document.getElementById("imageModal").style.display = "none";
        }

        // Close the modal when the Esc key is pressed
        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
                closeModal();
            }
        });

        // Disable right-click, copy, and print screen
        document.addEventListener("contextmenu", event => event.preventDefault());
        document.addEventListener("keydown", event => {
            if (event.key === "PrintScreen" || (event.ctrlKey && (["c", "v", "p", "x"].includes(event.key)))) {
                event.preventDefault();
                alert("Screenshot and copying are disabled to protect copyright.");
            }
        });
    </script>
</head>
<body>

<?php include '../includes/navigation-admin.php'; ?>
<div class="museum-background"></div>
<div class="gallery-container">
    <h1>All Featured Collections</h1>

    <!-- Add Collection and Back Buttons -->
    <div class="button-container">
        <a href="admin-museum-collection.php" class="button red-button">Add Collection</a>
        <a href="admin-museums.php" class="button grey-button">Back</a>
    </div>

    <?php if (count($collections) > 0): ?>
        <div class="gallery">
            <?php foreach ($collections as $collection): ?>
                <div class="gallery-item" onclick="openModal('<?php echo $collection['image_url']; ?>', '<?php echo addslashes($collection['title']); ?>', '<?php echo addslashes($collection['description']); ?>', '<?php echo addslashes($museumName); ?>')">
                    <img src="<?php echo htmlspecialchars($collection['image_url']); ?>" alt="<?php echo htmlspecialchars($collection['title']); ?>" class="gallery-image">
                    <div class="gallery-info">
                        <h3><?php echo htmlspecialchars($collection['title']); ?></h3>
                        <p><?php echo htmlspecialchars($collection['description']); ?></p>
                        <a href="admin-edit-collections.php?title=<?php echo urlencode($collection['title']); ?>&description=<?php echo urlencode($collection['description']); ?>&image_url=<?php echo urlencode($collection['image_url']); ?>" class="button red-button">Update</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="title" value="<?php echo htmlspecialchars($collection['title']); ?>">
                            <button type="submit" name="delete" class="button grey-button" onclick="return confirm('Are you sure you want to delete this collection?');">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No collections found.</p>
    <?php endif; ?>
</div>

<!-- Modal for Image Display -->
<div id="imageModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-image-container">
            <img id="modalImage" class="modal-image" src="" alt="">
            <div class="modal-watermark" id="modalWatermark"></div>
        </div>
        <div class="modal-info">
            <h3 id="modalTitle"></h3>
            <p id="modalDescription"></p>
        </div>
    </div>
</div>

</body>
</html>
