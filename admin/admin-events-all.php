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

// Pagination setup
$eventsPerPage = 2; // Number of events per page
$totalEvents = $conn->query("SELECT COUNT(*) FROM events")->fetch_row()[0];
$totalPages = ceil($totalEvents / $eventsPerPage);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($totalPages, $current_page)); // Ensure page is valid
$offset = ($current_page - 1) * $eventsPerPage;

// Fetch events for the current page
$sql = "SELECT * FROM events LIMIT $offset, $eventsPerPage"; // Fetch events with limit
$result = $conn->query($sql);

$events = [];
if ($result && $result->num_rows > 0) {
    // Fetch all events into an array
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - All News & Events</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* General container style */
        .container {
            background-color: white; /* White background */
            margin: 0 auto; /* Center container */
            padding: 20px; /* Padding for spacing */
            max-width: 1200px; /* Max width to prevent too wide content */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }

        /* Add some styles for the modal */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
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

        .event-card {
            cursor: pointer; /* Change cursor to pointer on hover */
            margin-bottom: 15px; /* Space between event cards */
            padding: 10px;
            border: 1px solid #ccc; /* Border for event cards */
            border-radius: 5px; /* Rounded corners */
            transition: box-shadow 0.3s; /* Smooth transition for hover effect */
        }

        .event-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect on hover */
        }

        .search-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: flex-end; /* Align search box to the right */
        }

        #searchInput {
            padding: 10px; /* Padding for search input */
            border: 1px solid #ccc; /* Border for search input */
            border-radius: 5px; /* Rounded corners */
            width: 250px; /* Width of search input */
            transition: border-color 0.3s; /* Smooth transition for border color */
        }

        #searchInput:focus {
            border-color: #007BFF; /* Change border color on focus */
            outline: none; /* Remove default outline */
        }

        .back-button-container {
            margin: 10px; /* Add some margin */
        }

        .back-button {
            background-color: #007BFF; /* Blue background */
            color: white; /* White text */
            padding: 10px 15px; /* Padding for button */
            border: none; /* Remove border */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor */
            font-size: 16px; /* Font size */
            text-decoration: none;
            transition: background-color 0.3s; /* Transition for hover effect */
        }

        .back-button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        .pagination {
            margin: 20px 0; /* Margin for pagination */
            display: flex;
            justify-content: center; /* Center pagination */
        }

        .pagination a {
            margin: 0 5px; /* Space between pagination links */
            padding: 10px; /* Padding for links */
            text-decoration: none; /* Remove underline */
            border: 1px solid #007BFF; /* Border for links */
            color: #007BFF; /* Blue text */
            border-radius: 5px; /* Rounded corners */
            transition: background-color 0.3s; /* Transition for hover effect */
        }

        .pagination a:hover {
            background-color: #007BFF; /* Blue background on hover */
            color: white; /* White text on hover */
        }

        .pagination span {
            margin: 0 5px; /* Space between pagination numbers */
            padding: 10px; /* Padding for numbers */
        }
        
    </style>
</head>
<body>
<div class="museum-background"></div>

<?php 
        include '../includes/navigation-admin.php';
    
?>

<!-- Back Button-->
<div class="back-button-container">
    <a href="admin-events.php" class="back-button">Back</a>
</div>

<!-- Main Content -->
<div class="container">
    <main>
        <section class="all-events-section">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>ALL EVENTS</h2>
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Search for events, museums, or dates..." onkeyup="filterEvents()">
                </div>
            </div>
            <div id="allEventsContainer">
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <div class="event-card" onclick="showEventDetails('<?php echo htmlspecialchars($event['title']); ?>', '<?php echo htmlspecialchars($event['museumName']); ?>', '<?php echo htmlspecialchars($event['date']); ?>', '<?php echo htmlspecialchars($event['description']); ?>', '<?php echo htmlspecialchars($event['image']); ?>')">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p><strong>Museum:</strong> <?php echo htmlspecialchars($event['museumName']); ?></p>
                            <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                            <?php if (!empty($event['image'])): ?>
                                <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" style="max-width: 100%;">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No events found.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>">&laquo; Previous</a>
                <?php endif; ?>

                <span>Page <?php echo $current_page; ?> of <?php echo $totalPages; ?></span>

                <?php if ($current_page < $totalPages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>

<!-- Modal for displaying event details -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Event Details</h2>
        <p><strong>What:</strong> <span id="eventTitle"></span></p>
        <p><strong>Where:</strong> <span id="eventMuseum"></span></p>
        <p><strong>When:</strong> <span id="eventDate"></span></p>
        <p><strong>Description:</strong> <span id="eventDescription"></span></p>
        <img id="eventImage" src="" alt="" style="max-width: 100%;">
    </div>
</div>

<!-- JavaScript to load the events -->
<script src="../js/script.js"></script> <!-- Link to your script -->

<script>
    function goBack() {
        window.history.back(); // Navigate to the previous page
    }

    function showEventDetails(title, museumName, date, description, image) {
        // Populate the modal with event details
        document.getElementById("eventTitle").innerText = title;
        document.getElementById("eventMuseum").innerText = museumName;
        document.getElementById("eventDate").innerText = date;
        document.getElementById("eventDescription").innerText = description;
        document.getElementById("eventImage").src = image;

        document.getElementById("eventModal").style.display = "block"; // Show the modal
    }

    function closeModal() {
        document.getElementById("eventModal").style.display = "none"; // Hide the modal
    }

    function filterEvents() {
        // Add filter functionality here
        let input = document.getElementById("searchInput").value.toLowerCase();
        let cards = document.querySelectorAll(".event-card");
        cards.forEach(card => {
            let title = card.querySelector("h3").innerText.toLowerCase();
            let museum = card.querySelector("p strong").nextSibling.textContent.toLowerCase();
            if (title.includes(input) || museum.includes(input)) {
                card.style.display = ""; // Show the card
            } else {
                card.style.display = "none"; // Hide the card
            }
        });
    }
</script>
</body>
</html>
