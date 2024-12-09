<?php 
session_start();
// Include your database connection
include '../includes/db_connections.php';

// Check if artwork ID is provided in the URL
if (isset($_GET['id'])) {
    $artworkId = mysqli_real_escape_string($conn, $_GET['id']);

    // Prepare and execute the SQL statement to fetch artwork details
    $query = "SELECT title, museumName, artistName, description, medium, dimension, email, websiteLink, contactNumber, imagePath 
              FROM artworks WHERE artworkID = ?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $artworkId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Fetch artwork details
        if ($artwork = mysqli_fetch_assoc($result)) {
            // Sanitize and store artwork details in variables
            $title = htmlspecialchars($artwork['title']);
            $museumName = htmlspecialchars($artwork['museumName']);
            $artistName = htmlspecialchars($artwork['artistName']);
            $description = htmlspecialchars($artwork['description']);
            $medium = htmlspecialchars($artwork['medium']);
            $dimensions = htmlspecialchars($artwork['dimension']);
            $email = htmlspecialchars($artwork['email']);
            $websiteLink = htmlspecialchars($artwork['websiteLink']);
            $contactNumber = htmlspecialchars($artwork['contactNumber']);
            $imagePath = htmlspecialchars($artwork['imagePath']);
        } else {
            echo "<p>Artwork not found.</p>";
            exit();
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<p>Error fetching artwork details.</p>";
        exit();
    }
} else {
    echo "<p>No artwork selected.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - ArtWork Details</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: grey;
            margin: 0;
            padding: 0;
        }
        
        .artwork-details {
            border: 1px solid #ccc;
            padding: 20px;
            background: whitesmoke;
            margin: 50px auto;
            max-width: 600px;
            text-align: center;
            position: relative;
        }

        .artwork-image {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
            cursor: pointer;
        }

        .close-button {
            position: absolute;
            top: 10px; 
            right: 10px;
            font-size: 24px;
            color: black;
            text-decoration: none;
        }

        .close-button:hover {
            color: black;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        canvas {
            max-width: 90%;
            max-height: 90%;
        }

        * {
            user-select: none;
        }

        .speech-controls {
            margin-top: 20px;
        }

        .speech-controls button {
            margin: 0 5px;
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .speech-controls button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body oncontextmenu="return false;">
<div class="museum-background"></div>

<?php 
    // Check if the user is logged in by checking for the session username
    if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        // If logged in, include the logged-in navbar
        include '../includes/navigation-loggedin.php';
    } else {
        // If not logged in, include the guest navbar
        include '../includes/navigation-guest.php';
    }
    ?>

<div class="artwork-details">
    <h1>Artwork Details</h1>
    <a href="guest-art.php" class="close-button">&times;</a>
    <h1><?php echo $title; ?></h1>
    <h1><strong></strong> <?php echo $museumName; ?></h1>
    <p><strong>Artist:</strong> <?php echo $artistName; ?></p>
    
    <img class="artwork-image" src="<?php echo $imagePath; ?>" alt="Image of <?php echo $title; ?>" id="artworkImg">
    
    <p><strong>Description:</strong> <?php echo $description; ?></p>
    <p><strong>Medium:</strong> <?php echo $medium; ?></p>
    <p><strong>Dimensions:</strong> <?php echo $dimensions; ?></p>
    <p><strong>Email:</strong> <?php echo $email; ?></p>
    <p><strong>Website:</strong> <a href="<?php echo $websiteLink; ?>" target="_blank" rel="noopener"><?php echo $websiteLink; ?></a></p>
    <p><strong>Contact Number:</strong> <?php echo $contactNumber; ?></p>

    <div class="speech-controls">
        <button onclick="speakText('Title: <?php echo $title; ?>. Description: <?php echo $description; ?>')">Read Details</button>
    </div>
</div>

<div class="overlay" id="imageOverlay">
    <canvas id="zoomedCanvas"></canvas>
</div>

<script>
    let speech = null;

    document.onkeydown = function(e) {
        if (e.ctrlKey && (e.key === 'p' || e.key === 'c' || e.key === 's')) {
            e.preventDefault();
        }
    };

    document.oncontextmenu = function() {
        return false;
    };

    const artworkImg = document.getElementById('artworkImg');
    const imageOverlay = document.getElementById('imageOverlay');
    const zoomedCanvas = document.getElementById('zoomedCanvas');
    const ctx = zoomedCanvas.getContext('2d');

    artworkImg.onclick = function() {
    const img = new Image();
    img.src = artworkImg.src;
    img.onload = function() {
        zoomedCanvas.width = img.width;
        zoomedCanvas.height = img.height;
        ctx.drawImage(img, 0, 0);
        
        // Watermark Text Settings
        const watermarkText = '<?php echo $museumName; ?>';
        const fontSize = 30;  // Adjust this size based on your preference
        ctx.font = `bold ${fontSize}px Arial`;  // Font style and size
        ctx.textAlign = 'center';  // Center the text horizontally
        ctx.textBaseline = 'middle';  // Center the text vertically
        const textX = zoomedCanvas.width / 2;  // X position of the text
        const textY = zoomedCanvas.height / 2;  // Y position of the text

        // Measure the width and height of the text
        const textWidth = ctx.measureText(watermarkText).width;
        const textHeight = fontSize;  // Approximate height of the text (font size)

        // Add the border box around the watermark text
        const padding = 10;  // Add some padding around the text
        const boxX = textX - textWidth / 2 - padding;
        const boxY = textY - textHeight / 2 - padding;
        const boxWidth = textWidth + 2 * padding;
        const boxHeight = textHeight + 2 * padding;

        // Draw the border box (rectangular box around text)
        ctx.lineWidth = 5;  // Border width of the box
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.7)';  // Border color (light transparent white)
        ctx.strokeRect(boxX, boxY, boxWidth, boxHeight);  // Draw the border box

        // Add the watermark text (fill text inside the border box)
        ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';  // Watermark color with transparency
        ctx.fillText(watermarkText, textX, textY);  // Draw the watermark text

        // Show the overlay with the zoomed image and watermark
        imageOverlay.style.display = 'flex';
    };
};



    imageOverlay.onclick = function() {
        imageOverlay.style.display = 'none';
    };

    // Speech functions
    function speakText(text) {
    if (speech) {
        window.speechSynthesis.cancel(); // Stop any ongoing speech
    }
    speech = new SpeechSynthesisUtterance(text);
    speech.lang = 'en-US'; // Set language

    // Set voice to a more human-like option, if available
    let voices = window.speechSynthesis.getVoices();
    let selectedVoice = null;
    
    // Try to select a more natural voice
    voices.forEach(function(voice) {
        if (voice.name === 'Google UK English Female' || voice.name === 'Google US English') {
            selectedVoice = voice;
        }
    });

    if (selectedVoice) {
        speech.voice = selectedVoice;
    }

    // Set additional voice properties for a more natural sound
    speech.pitch = 1;  // Normal pitch (can be adjusted for a more natural sound)
    speech.rate = 1.0;   // Normal speed (can be adjusted for a more natural rhythm)
    speech.volume = 1;   // Volume (0 to 1, where 1 is maximum)

    window.speechSynthesis.speak(speech);
}

    function pauseSpeech() {
        window.speechSynthesis.pause();
    }

    function resumeSpeech() {
        window.speechSynthesis.resume();
    }
</script>

</body>
</html>
