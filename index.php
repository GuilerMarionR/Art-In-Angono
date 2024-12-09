<?php
session_start(); // Start the session to check login status
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
    /* General Styles for the welcome-content */
.background-container {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f5f5f5; /* Optional: Set a background color */
    padding: 50px 20px;
}

.welcome-content {
    display: flex;
    flex-direction: row; /* Places image and text side by side */
    align-items: center; /* Vertically centers content */
    gap: 20px; /* Adds space between image and text */
    max-width: 1200px; /* Limits the overall width of the section */
    margin-right: 200px;
}

/* Styling the text section */
.welcome-text {
    margin-bottom: 150px;
    flex: 1;
    text-align: left; /* Aligns text to the left */
}

.heading {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 10px;
}

.subheading {
    font-size: 1.2rem;
    line-height: 1.6;
}

/* Styling the image */
.welcome-image img {
    max-width: 55%;
    height: auto; /* Maintains aspect ratio */
    margin-bottom: 150px;
    border-radius: 10px; /* Optional: Adds rounded corners */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Optional: Adds a subtle shadow */
    flex: 1; /* Ensures equal space with the text */
}

/* Responsive design for smaller screens */
@media (max-width: 768px) {
    .welcome-content {
        flex-direction: column; /* Stacks text and image vertically */
        text-align: center;
    }

    .welcome-text {
        margin-bottom: 20px; /* Adds space between text and image */
    }

    .welcome-image img {
        max-width: 80%; /* Reduces image size on smaller screens */
    }
}
</style>
<body>

      <!-- Navigation Bar -->
      <div class="navbar">
        <!-- Logo Section -->
        <div class="logo">
            <img src="https://i.imgur.com/BeaSiLw.png" alt="Logo" class="logo-img">
            <span>ART IN ANGONO</span>
        </div>
        
        <!-- Menu Section -->
        <div class="menu">
            <a href="index.php">HOME</a>
            <a href="guests/guest-museums.php">MUSEUMS</a>
            <a href="guests/guest-news.php">NEWS & EVENTS</a>
            <a href="guests/guest-art.php">ARTWORKS</a>
            <a href="guests/guest-book.php">BOOK A TOUR</a>

            <?php if (isset($_SESSION['username'])): ?>
                <!-- User is logged in, show logout link -->
                <a href="logins/logout.php">LOGOUT</a>
            <?php else: ?>
                <!-- User is not logged in, show login link -->
                <a href="logins/login.php">LOGIN</a>
            <?php endif; ?>
        </div>
      </div>

      <div class="background-container">
    <div class="welcome-content">
        <div class="welcome-text">
            <h1 class="heading">WELCOME TO ART IN ANGONO</h1>
            <p class="subheading">Learn about our rich cultural heritage through 360-degree virtual tours, interactive exhibits, and specially curated collections that celebrate the genius of Filipino artists. Start your artistic journey now and experience the magic of Angono's unparalleled creativity.</p>
        </div>
        <div class="welcome-image">
            <img src="Assets/index.jpg" alt="Welcome Image">
        </div>
    </div>
</div>


<!-- Download Section-->
<section class="app-download-section">
    <div class="app-content">
        <img src="https://i.imgur.com/gRrh04i.png" alt="Art in Angono App">
        <div class="app-text">
            <h3>Download Now!</h3>
            <p>Discover Angono, the Art Capital of the Philippines, with our ultimate museum guide! Navigate effortlessly using Suggestive Mapping for personalized routes or Manual Mapping to explore at your own pace. Uncover local art, history, and culture with tailored routes, nearby museum alerts, and exclusive insights—all in one app. Dive into Angono's vibrant art scene today!</p>
            <a href="https://art-in-angono.netlify.app/" target="_blank">
            <button>Art in Angono App</button>
            </a>
        </div>
    </div>
</section>

    <!-- JavaScript file -->
    <script src="../js/script.js"></script>
</body>
</html>
