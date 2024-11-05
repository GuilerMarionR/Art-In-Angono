<?php 
session_start(); // Start the session

// Include your database connection
include '../includes/db_connections.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the username from the session
$username = $_SESSION['username'];

// Prepare and execute the query to fetch museum information
$sql = "SELECT * FROM museums WHERE name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch museum info
    $museum = $result->fetch_assoc();
    $museumName = htmlspecialchars($museum['name']);
    $museumHistory = htmlspecialchars($museum['history']);
    $museumDescription = htmlspecialchars($museum['description']);
} else {
    $museumName = "Museum not found";
    $museumHistory = "No history available.";
    $museumDescription = "No description available.";
}

// Fetch views associated with this museum
$views = [];
$sql = "SELECT 360_URL FROM views WHERE museumName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $museumName);
$stmt->execute();
$result = $stmt->get_result();

while ($view = $result->fetch_assoc()) {
    $views[] = [
        'url' => htmlspecialchars($view['360_URL']),
    ];
}

$stmt->close();

// Fetch collections associated with this museum
$collections = [];
$sql = "SELECT image_url, description FROM collections WHERE museumName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $museumName);
$stmt->execute();
$result = $stmt->get_result();

while ($collection = $result->fetch_assoc()) {
    $collections[] = [
        'image' => htmlspecialchars($collection['image_url']),
        'description' => htmlspecialchars($collection['description'])
    ];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art In Angono</title>
    <link rel="stylesheet" href="../css/admin-museums.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .featured-collections {
            padding: 20px;
            text-align: center;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8);
        }
        .modal-content {
            margin: auto;
            display: block;
            max-width: 80%;
            max-height: 80%;
            position: relative;
        }
        .close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
        }
        .navigation-arrows {
    display: flex;
    justify-content: space-between;
    position: absolute;
    top: 50%; /* Center vertically */
    width: 100%;
    transform: translateY(-50%);
    pointer-events: none; /* Ensures arrows don't interfere with other elements */
}

.prev, .next {
    cursor: pointer;
    padding: 16px;
    color: white;
    font-weight: bold;
    font-size: 24px;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.6);
    pointer-events: auto; /* Enables clicking on the arrows */
    transition: background-color 0.3s ease;
}

.prev:hover, .next:hover {
    background-color: rgba(0, 0, 0, 0.8);
}


        #slideshow-image {
            width: 300px;
            height: 200px;
            object-fit: cover;
        }
        .button-container {
            text-align: right;
            margin-bottom: 10px;
        }
        .museum-details {
            text-align: center;
            padding: 20px;
        }
        .thumbnail-container {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        .thumbnail {
            width: 100px;
            height: 75px;
            object-fit: cover;
            margin: 0 5px;
            cursor: pointer;
        }
        .collection-item {
            display: none;
        }
        .collection-item.active {
            display: block;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: rgba(255, 255, 255, 0.5);
            font-size: 50px;
            pointer-events: none;
            white-space: nowrap;
        }
        .featured-collections-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px; /* Adds some space below the header */
}

.featured-collections h2 {
    font-size: 50px;
    font-weight: bold;
    margin: 0; /* Removes default margin */
    color: #333; /* Customize color as needed */
}

.view-all-button {
    font-size: 20px;
    color: white; /* Button text color */
    background-color: #C1121F; /* Button background color */
    border: none; /* Remove default border */
    border-radius: 5px; /* Rounded corners */
    padding: 10px 20px; /* Padding for button size */
    cursor: pointer; /* Pointer cursor on hover */
    transition: background-color 0.3s ease; /* Smooth transition */
}

.view-all-button:hover {
    background-color: grey; /* Darker shade on hover */
}

    </style>
</head>
<body oncontextmenu="return false;">
    <script>
        document.addEventListener("keydown", function(event) {
            if (event.ctrlKey && (event.key === 'p' || event.key === 'P')) {
                event.preventDefault();
            }
        });

        document.addEventListener("copy", function(event) {
            event.preventDefault();
        });
    </script>
    <div class="museum-background"></div>
    <?php include '../includes/navigation-admin.php'; ?>

    <div class="iframe-container">
        <?php if (!empty($views)): ?>
            <iframe id="main-iframe" src="<?php echo $views[0]['url']; ?>" width="100%" height="500px" frameborder="0"></iframe>
        <?php else: ?>
            <p>No 360 view available for this museum.</p>
        <?php endif; ?>
    </div>

    <div class="museum-details">
        <div class="button-container">
            <a class="action-button edit-button" href="admin-museum-update-info.php">Edit</a>
        </div>
        <h2><?php echo $museumName; ?></h2>
        <h3>History</h3>
        <p><?php echo $museumHistory; ?></p>

        <h3>Description</h3>
        <p><?php echo $museumDescription; ?></p>
    </div>

    <div class="featured-collections">
    <header class="featured-collections-header">
        <h2>Featured Collections</h2>
        <a href="admin-collection.php" class="view-all-button">View All</a>
    </header>
    <div id="slideshow-container" style="position: relative;">
        <?php foreach ($collections as $index => $collection): ?>
            <div class="collection-item <?php if ($index === 0) echo 'active'; ?>">
                <img src="<?php echo $collection['image']; ?>" alt="Collection Image" class="collection-image" id="slideshow-image" onclick="openModal(this)">
                <p><?php echo $collection['description']; ?></p>
            </div>
        <?php endforeach; ?>

        <!-- Navigation arrows -->
        <div class="navigation-arrows">
            <span class="prev" onclick="changeCollection(-1)">&#10094;</span>
            <span class="next" onclick="changeCollection(1)">&#10095;</span>
        </div>
    </div>
</div>


    <div id="myModal" class="modal" onclick="closeModal()">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modal-image">
        <div class="watermark"><?php echo $museumName; ?></div>
    </div>

    <script>
        let currentCollectionIndex = 0;

        function changeCollection(direction) {
            const collections = document.querySelectorAll('.collection-item');
            collections[currentCollectionIndex].classList.remove('active');
            currentCollectionIndex += direction;

            if (currentCollectionIndex >= collections.length) {
                currentCollectionIndex = 0;
            } else if (currentCollectionIndex < 0) {
                currentCollectionIndex = collections.length - 1;
            }

            collections[currentCollectionIndex].classList.add('active');
        }

        function openModal(element) {
            const modal = document.getElementById("myModal");
            const modalImage = document.getElementById("modal-image");
            modal.style.display = "block";
            modalImage.src = element.src;
        }

        function closeModal() {
            const modal = document.getElementById("myModal");
            modal.style.display = "none";
        }
    </script>
</body>
</html>
