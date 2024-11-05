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

// Get today's date and calculate future dates
$today = new DateTime();
$currentEventLimit = (clone $today)->modify('+2 days'); // 2 days from now
$upcomingEventLimit = (clone $today)->modify('+30 days'); // 30 days from now

// Fetch events from the database
$events = [];
$sql = "SELECT * FROM events WHERE date >= CURDATE()"; // Fetch only events from today onward
$result = $conn->query($sql);

// Check if the query executed successfully
if ($result && $result->num_rows > 0) {
    // Fetch all events into an array
    while ($row = $result->fetch_assoc()) {
        $eventDate = new DateTime($row['date']);
        $events[] = [
            'title' => $row['title'],
            'museumName' => $row['museumName'],
            'date' => $row['date'],
            'description' => $row['description'],
            'image' => $row['image'],
            'eventDate' => $eventDate
        ];
    }
}

// Separate events into current and upcoming
$currentEvents = [];
$upcomingEvents = [];

foreach ($events as $event) {
    if ($event['eventDate'] <= $currentEventLimit) {
        $currentEvents[] = $event; // Current event (up to 2 days)
    } elseif ($event['eventDate'] <= $upcomingEventLimit) {
        $upcomingEvents[] = $event; // Upcoming event (3 to 30 days)
    }
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .event-card {
            display: none; /* Hide all events by default */
            cursor: pointer; /* Change cursor to pointer */
        }
        .event-card.active {
            display: block; /* Show the active event */
        }
        .navigation-buttons {
            display: flex;
            justify-content: center; /* Center the buttons */
            margin-top: 20px;
        }
        .arrow-button {
            background: none;
            border: none;
            font-size: 24px; /* Increase font size for visibility */
            cursor: pointer;
            margin: 0 15px; /* Space between buttons */
            transition: transform 0.2s; /* Animation for button hover */
        }
        .arrow-button:hover {
            transform: scale(1.2); /* Slightly enlarge on hover */
        }

        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Remove link decorations */
        a {
            text-decoration: none; /* Remove underline */
            color: inherit; /* Use the current color (or set to a specific color if needed) */
        }

        a:hover {
            color: inherit; /* Maintain the current color on hover */
        }
    </style>
</head>
<body>
<div class="museum-background"></div>
    <?php include '../includes/navigation-admin.php'; ?>

    <main>
        <!-- Events Section -->
        <section class="events-section">
            <div class="section-header">
                <h2>NEWS & EVENTS</h2>
                <a href="admin-events-all.php" class="view-all-btn">View All Events</a>
            </div>
            <div class="events-wrapper" id="guestEventsContainer">
                <?php if (!empty($currentEvents)): ?>
                    <div class="event-card active" id="currentEventCard" onclick="openCurrentModal()">
                        <h3 id="currentEventTitle"><?php echo $currentEvents[0]['title']; ?></h3>
                        <p id="currentEventMuseumName"><?php echo $currentEvents[0]['museumName']; ?></p>
                        <p id="currentEventDate"><?php echo $currentEvents[0]['date']; ?></p>
                        <img id="currentEventImage" src="<?php echo $currentEvents[0]['image']; ?>" alt="" style="max-width: 100%;">
                    </div>
                <?php else: ?>
                    <p>No current events found.</p>
                <?php endif; ?>
            </div>
            <div class="navigation-buttons">
                <button class="arrow-button" id="currentPrevButton" onclick="showCurrentPrevEvent()">&#9664;</button> <!-- Left Arrow -->
                <button class="arrow-button" id="currentNextButton" onclick="showCurrentNextEvent()">&#9654;</button> <!-- Right Arrow -->
            </div>
        </section>

        <!-- Upcoming Events Section -->
        <section class="upcoming-events-section">
            <div class="section-header">
                <h2>UPCOMING EVENTS</h2>
            </div>
            <div class="upcoming-events-wrapper" id="guestUpcomingEventsContainer">
                <?php if (!empty($upcomingEvents)): ?>
                    <div class="event-card active" id="upcomingEventCard" onclick="openUpcomingModal()">
                        <h3 id="upcomingEventTitle"><?php echo $upcomingEvents[0]['title']; ?></h3>
                        <p id="upcomingEventMuseumName"><?php echo $upcomingEvents[0]['museumName']; ?></p>
                        <p id="upcomingEventDate"><?php echo $upcomingEvents[0]['date']; ?></p>
                        <img id="upcomingEventImage" src="<?php echo $upcomingEvents[0]['image']; ?>" alt="" style="max-width: 100%;">
                    </div>
                <?php else: ?>
                    <p>No upcoming events found.</p>
                <?php endif; ?>
            </div>
            <div class="navigation-buttons">
                <button class="arrow-button" id="upcomingPrevButton" onclick="showUpcomingPrevEvent()">&#9664;</button> <!-- Left Arrow -->
                <button class="arrow-button" id="upcomingNextButton" onclick="showUpcomingNextEvent()">&#9654;</button> <!-- Right Arrow -->
            </div>
        </section>
    </main>

    <!-- Modal Structure -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 id="modalEventTitle"></h3>
            <p><strong>Where:</strong> <span id="modalEventMuseumName"></span></p>
            <p><strong>When:</strong> <span id="modalEventDate"></span></p>
            <img id="modalEventImage" src="" alt="" style="max-width: 100%;">
            <p><strong>Description</strong> <span id="modalEventDescription"></span></p>
        </div>
    </div>

    <script src="../js/script.js"></script> <!-- Include the guest JS file -->
    <script>
        // Slideshow functionality for current events
        const currentEvents = <?php echo json_encode($currentEvents); ?>; // Get current events as JSON
        let currentIndex = 0;

        // Function to open modal with current event details
        function openCurrentModal() {
            const event = currentEvents[currentIndex];
            updateModal(event);
            const modal = document.getElementById("eventModal");
            modal.style.display = "block"; // Show the modal
        }

        // Function to open modal with upcoming event details
        function openUpcomingModal() {
            const event = upcomingEvents[upcomingIndex];
            updateModal(event);
            const modal = document.getElementById("eventModal");
            modal.style.display = "block"; // Show the modal
        }

        // Function to update the modal content
        function updateModal(event) {
            document.getElementById('modalEventTitle').innerText = event.title;
            document.getElementById('modalEventDescription').innerText = event.description;
            document.getElementById('modalEventMuseumName').innerText = event.museumName;
            document.getElementById('modalEventDate').innerText = event.date;
            document.getElementById('modalEventImage').src = event.image;
        }

        // Close the modal
        function closeModal() {
            const modal = document.getElementById("eventModal");
            modal.style.display = "none"; // Hide the modal
        }

        // Functions to navigate through events
        function showCurrentPrevEvent() {
            currentIndex = (currentIndex === 0) ? currentEvents.length - 1 : currentIndex - 1;
            updateCurrentEventDisplay();
        }

        function showCurrentNextEvent() {
            currentIndex = (currentIndex + 1) % currentEvents.length;
            updateCurrentEventDisplay();
        }

        function updateCurrentEventDisplay() {
            const event = currentEvents[currentIndex];
            document.getElementById('currentEventTitle').innerText = event.title;
            document.getElementById('currentEventMuseumName').innerText = event.museumName;
            document.getElementById('currentEventDate').innerText = event.date;
            document.getElementById('currentEventImage').src = event.image;
        }

        function showUpcomingPrevEvent() {
            upcomingIndex = (upcomingIndex === 0) ? upcomingEvents.length - 1 : upcomingIndex - 1;
            updateUpcomingEventDisplay();
        }

        function showUpcomingNextEvent() {
            upcomingIndex = (upcomingIndex + 1) % upcomingEvents.length;
            updateUpcomingEventDisplay();
        }

        function updateUpcomingEventDisplay() {
            const event = upcomingEvents[upcomingIndex];
            document.getElementById('upcomingEventTitle').innerText = event.title;
            document.getElementById('upcomingEventMuseumName').innerText = event.museumName;
            document.getElementById('upcomingEventDate').innerText = event.date;
            document.getElementById('upcomingEventImage').src = event.image;
        }

        // Hide modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById("eventModal");
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>

