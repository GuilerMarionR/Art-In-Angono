<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - Collection Gallery</title>
    <link rel="stylesheet" href="../css/style.css">
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

        /* Search Input Styling */
        .search-container {
            margin-bottom: 20px;
        }

        .search-input {
            padding: 10px;
            width: 300px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        /* Modal Styling */
        #imageModal {
            display: none;
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
            position: relative;
            width: 100%;
            height: auto;
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
            bottom: 20px; /* Position at the bottom */
            right: 20px; /* Position at the right */
            font-size: 2em;
            color: rgba(255, 255, 255, 0.5); /* Semi-transparent white */
            pointer-events: none; /* Prevent mouse events */
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.7); /* Add shadow for better visibility */
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
    </style>
    <script>
        let speech = null;

        function openModal(imageSrc, title, description, museumName) {
            document.getElementById("imageModal").style.display = "flex";
            document.getElementById("modalImage").src = imageSrc;
            document.getElementById("modalTitle").innerText = title;
            document.getElementById("modalDescription").innerText = description;
            document.getElementById("modalWatermark").innerText = museumName; // Set the watermark text

            // Speak the title and description
            speakText(`Title: ${title}. Description: ${description}.`);

            // Close modal on background click
            document.getElementById("imageModal").onclick = function(event) {
                if (event.target === document.getElementById("imageModal")) {
                    closeModal();
                }
            };
        }

        function closeModal() {
            window.speechSynthesis.cancel(); // Stop speech when closing the modal
            document.getElementById("imageModal").style.display = "none";
        }

        // Speech functions
        function speakText(text) {
            if (speech) {
                window.speechSynthesis.cancel(); // Stop any ongoing speech
            }
            speech = new SpeechSynthesisUtterance(text);
            speech.lang = 'en-US'; // Set language
            window.speechSynthesis.speak(speech);
        }

        function pauseSpeech() {
            window.speechSynthesis.pause();
        }

        function resumeSpeech() {
            window.speechSynthesis.resume();
        }

        // Close modal when Esc key is pressed
        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
                closeModal();
            }
        });

        // Search Functionality
        function searchCollections() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const galleryItems = document.querySelectorAll('.gallery-item');

            galleryItems.forEach(item => {
                const title = item.querySelector('h3').innerText.toLowerCase();
                if (title.includes(input)) {
                    item.style.display = 'block'; // Show matching items
                } else {
                    item.style.display = 'none'; // Hide non-matching items
                }
            });
        }

        // Disable right-click and copying
        document.addEventListener("contextmenu", event => event.preventDefault());
        document.addEventListener("keydown", event => {
            if (event.key === "PrintScreen" || (event.ctrlKey && (event.key === "c" || event.key === "p"))) {
                event.preventDefault();
                alert("Screenshot and copying are disabled.");
            }
        });
    </script>
</head>
<body>

<?php include '../includes/navigation-guest.php'; ?>

<?php
// Initialize $collections as an empty array
$collections = [];

// Database connection and fetching logic
$conn = new mysqli('localhost', 'root', '', 'art_in_angono_db');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the museum ID from the URL and validate
$museum_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($museum_id > 0) {
    // Fetch the museum name to filter collections using a prepared statement
    $museum_sql = $conn->prepare("SELECT name FROM museums WHERE id = ?");
    $museum_sql->bind_param("i", $museum_id);
    $museum_sql->execute();
    $museum_result = $museum_sql->get_result();

    if ($museum_result->num_rows > 0) {
        $museum = $museum_result->fetch_assoc();
        $museum_name = $museum['name'];

        // Fetch all featured collections for this museum
        $collection_sql = $conn->prepare("SELECT * FROM collections WHERE museumName = ?");
        $collection_sql->bind_param("s", $museum_name);
        $collection_sql->execute();
        $collections_result = $collection_sql->get_result();

        // Fetch the collections into the $collections array
        while ($row = $collections_result->fetch_assoc()) {
            $collections[] = $row; // Populate $collections with each collection
        }
    } else {
        echo "<p>Museum not found.</p>";
    }

    $museum_sql->close();
} else {
    echo "<p>Invalid museum ID.</p>";
}

$conn->close();
?>

<!-- Search Input -->
<div class="gallery-container">
    <h1>All Featured Collections</h1>
    <div class="search-container" style="text-align: right;">
        <input type="text" id="searchInput" class="search-input" placeholder="Search by title..." onkeyup="searchCollections()">
    </div>

    <?php if (count($collections) > 0): ?>
        <div class="gallery">
            <?php foreach ($collections as $collection): ?>
                <div class="gallery-item" 
                     onclick="openModal('<?php echo htmlspecialchars($collection['image_url']); ?>', '<?php echo addslashes($collection['title']); ?>', '<?php echo addslashes($collection['description']); ?>', '<?php echo addslashes($museum_name); ?>')">
                    <img src="<?php echo htmlspecialchars($collection['image_url']); ?>" alt="<?php echo htmlspecialchars($collection['title']); ?>" class="gallery-image">
                    <div class="gallery-info">
                        <h3><?php echo htmlspecialchars($collection['title']); ?></h3>
                        <p><?php echo htmlspecialchars($collection['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No featured collections found for this museum.</p>
    <?php endif; ?>
</div>

<div id="imageModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-image-container">
            <img id="modalImage" alt="Zoomed Collection Image" class="modal-image">
            <div class="modal-watermark" id="modalWatermark"></div> <!-- Watermark for the museum name -->
        </div>
        <div class="modal-info">
            <h3>Title: <span id="modalTitle"></span></h3>
            <p>Description: <span id="modalDescription"></span></p>
            <button onclick="pauseSpeech()">Pause</button>
            <button onclick="resumeSpeech()">Resume</button>
        </div>
    </div>
</div>

</body>
</html>
