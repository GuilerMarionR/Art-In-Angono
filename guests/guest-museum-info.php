<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - Museum Details</title>
    <link rel="stylesheet" href="../css/guest-museum.css">
    <link rel="stylesheet" href="../css/style.css">
    <script>
        let speech = null;

        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        window.onbeforeprint = function() {
            return false;
        };

        document.onkeydown = function(e) {
            if (e.ctrlKey || e.metaKey) {
                if (e.key === 'p' || e.key === 's' || e.key === 'a') {
                    e.preventDefault();
                }
            }
        };

        function speakText(text) {
            if (speech) {
                window.speechSynthesis.cancel(); // Stop any ongoing speech
            }
            speech = new SpeechSynthesisUtterance(text);
            speech.lang = 'en-US'; // Set language
            window.speechSynthesis.speak(speech);
        }

        function readMuseumDetails() {
            const name = document.getElementById("display-name").innerText;
            const history = document.getElementById("display-history").innerText;
            const description = document.getElementById("display-description").innerText;
            const textToRead = `Museum Name: ${name}. History: ${history}. Description: ${description}.`;
            speakText(textToRead);
        }

        function pauseSpeech() {
            window.speechSynthesis.pause();
        }

        function resumeSpeech() {
            window.speechSynthesis.resume();
        }
    </script>
</head>
<body>

    <?php include '../includes/navigation-guest.php'; ?>

    <?php
    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'art_in_angono_db');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the museum ID from the URL
    $museum_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Fetch the specific museum's details
    $sql = "SELECT * FROM museums WHERE id = $museum_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $museum = $result->fetch_assoc();
    } else {
        echo "Museum not found.";
        exit;
    }

    // Fetch the featured collections based on the museum name
    $museum_name = $conn->real_escape_string($museum['name']);
    $collection_sql = "SELECT * FROM collections WHERE museumName = '$museum_name'";
    $collections_result = $conn->query($collection_sql);

    // Fetch the 3D views from the views table
    $views_sql = "SELECT * FROM views WHERE museumName = '$museum_name'";
    $views_result = $conn->query($views_sql);

    // Get the first 360_URL if available
    $initial_360_url = '';
    if ($views_result->num_rows > 0) {
        $first_view = $views_result->fetch_assoc();
        $initial_360_url = $first_view['360_URL'];
    }

    // Close the database connection
    $conn->close();
    ?>
    <div class="museum-background"></div>
    <div class="image-container">
        <iframe id="main-iframe" src="<?php echo htmlspecialchars($initial_360_url); ?>" width="100%" height="600" style="border:none;" title="3D View"></iframe>
    </div>

    <div class="museum-details">
        <h2 id="display-name"><?php echo htmlspecialchars($museum['name']); ?></h2>

        <h3>HISTORY</h3>
        <p id="display-history"><?php echo htmlspecialchars($museum['history']); ?></p>

        <h3>DESCRIPTION</h3>
        <p id="display-description"><?php echo htmlspecialchars($museum['description']); ?></p>
        
        <!-- Button to trigger text-to-speech -->
        <button onclick="readMuseumDetails()">Read Museum Details</button>
        <button onclick="pauseSpeech()">Pause</button>
        <button onclick="resumeSpeech()">Resume</button>
    </div>

    <div class="featured-collections">
        <h2>FEATURED COLLECTIONS</h2>

        <a href="guest-collection.php?id=<?php echo $museum_id; ?>" class="redirect-button">View All Collections</a>

        <div class="slideshow-container">
            <?php if ($collections_result->num_rows > 0): ?>
                <?php while ($collection = $collections_result->fetch_assoc()): ?>
                    <div class="slides">
                        <img src="<?php echo htmlspecialchars($collection['image_url']); ?>" alt="<?php echo htmlspecialchars($collection['title']); ?>" class="featured-image" onclick="showImage(this)">
                        <h3 class="collection-title"><?php echo htmlspecialchars($collection['title']); ?></h3>
                        <p class="collection-description"><?php echo htmlspecialchars($collection['description']); ?></p>
                    </div>
                <?php endwhile; ?>
                <div class="navigation-arrows">
                    <a class="prev" onclick="changeSlide(-1)">&#9664;</a> <!-- Left arrow -->
                    <a class="next" onclick="changeSlide(1)">&#9654;</a> <!-- Right arrow -->
                </div>
            <?php else: ?>
                <p>No featured collections found for this museum.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="overlay" id="overlay">
        <img id="overlay-image" src="" alt="Zoomed Image">
        <div class="watermark"><?php echo htmlspecialchars($museum['name']); ?></div> <!-- Dynamic museum name in watermark -->
    </div>

    <script>
        let slideIndex = 0;
        showSlides(slideIndex);

        function showSlides(index) {
            const slides = document.getElementsByClassName("slides");
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none"; // Hide all slides
            }
            if (slides.length > 0) {
                slides[index].style.display = "block"; // Show the current slide
            }
        }

        function changeSlide(n) {
            slideIndex += n;
            const slides = document.getElementsByClassName("slides");
            if (slideIndex >= slides.length) { slideIndex = 0; }
            if (slideIndex < 0) { slideIndex = slides.length - 1; }
            showSlides(slideIndex);
        }

        function showImage(img) {
            const overlay = document.getElementById("overlay");
            const overlayImage = document.getElementById("overlay-image");
            overlayImage.src = img.src; // Set the overlay image source to the clicked image source
            overlay.style.display = "flex"; // Show the overlay
        }

        // Hide the overlay when clicked
        document.getElementById("overlay").addEventListener("click", function() {
            this.style.display = "none";
        });
    </script>

</body>
</html>
