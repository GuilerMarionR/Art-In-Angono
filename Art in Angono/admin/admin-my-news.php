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

// Get the logged-in user's username
$currentUsername = $_SESSION['username'];

// Fetch events from the database that match the user's museum name
$events = [];
$sql = "SELECT * FROM events WHERE museumName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $currentUsername);
$stmt->execute();
$result = $stmt->get_result();

// Check if the query executed successfully
if ($result && $result->num_rows > 0) {
    // Fetch all events into an array
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Handle delete event request
if (isset($_POST['deleteEvent'])) {
    $eventID = $_POST['eventID'];
    $deleteSql = "DELETE FROM events WHERE eventID = ? AND museumName = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("ss", $eventID, $currentUsername);
    
    if ($deleteStmt->execute()) {
        // Redirect to the same page to refresh the events list
        header("Location: admin-my-news.php"); 
        exit();
    }
    
    $deleteStmt->close();
}

$conn->close(); // Close the database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - ADMIN</title>
    <style>
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

        /* Event card styles */
        .event-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px;
            width: 250px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        /* Button styling */
        .event-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .update-btn {
            background-color: red;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
            flex: 1;
        }

        .delete-btn {
            background-color: grey;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
            flex: 1;
        }

        .update-btn:hover {
            background-color: darkred;
        }

        .delete-btn:hover {
            background-color: darkgrey;
        }
    </style>
</head>
<body>
    
    <div class="navbar">
        <?php include '../includes/navigation-admin.php'; ?>
    </div>
    <div class="museum-background"></div>
        <div class="admin-news-content">
            <div class="button-container">
                <button class="action-button" onclick="location.href='admin-add-event.php'">Add Event</button>
                <button class="action-button" onclick="location.href='admin-my-news.php'">View All Events</button>
         </div>
            <div class="news-events" id="news-events">
                <div id="myEvents" class="events-container" style="display: flex; flex-wrap: wrap; justify-content: left;">
                    <?php if (!empty($events)): ?>
                        <?php foreach ($events as $event): ?>
                            <div class="event-card">
                                <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p><?php echo htmlspecialchars($event['museumName']); ?></p>
                                <p><?php echo htmlspecialchars($event['date']); ?></p>
                                <p><?php echo htmlspecialchars($event['description']); ?></p>
                                <?php if (!empty($event['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" style="max-width: 100%; height: auto;">
                                <?php endif; ?>
                                <div class="event-buttons">
                                    <button class="update-btn" onclick="location.href='admin-update-news.php?eventID=<?php echo htmlspecialchars($event['eventID']); ?>'">Update Event</button>
                                    <button class="delete-btn" onclick="confirmDelete('<?php echo htmlspecialchars($event['eventID']); ?>')">Delete Event</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No events found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Delete</h2>
            <p>Are you sure you want to delete this event?</p>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="eventID" id="eventID">
                <div class="modal-buttons">
                    <button type="submit" name="deleteEvent" class="delete-btn">Delete</button>
                    <button type="button" id="cancelDelete" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Structure for Event Details -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 id="modalEventTitle"></h3>
            <p><strong>What:</strong> <span id="modalEventDescription"></span></p>
            <p><strong>Where:</strong> <span id="modalEventMuseumName"></span></p>
            <p><strong>When:</strong> <span id="modalEventDate"></span></p>
            <img id="modalEventImage" src="" alt="" style="max-width: 100%;">
        </div>
    </div>

    <script>
        function confirmDelete(eventID) {
            document.getElementById('eventID').value = eventID;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
            document.getElementById('eventModal').style.display = 'none';
        }

        function openEventModal(event) {
            document.getElementById('modalEventTitle').innerText = event.title;
            document.getElementById('modalEventDescription').innerText = event.description;
            document.getElementById('modalEventMuseumName').innerText = event.museumName;
            document.getElementById('modalEventDate').innerText = event.date;
            document.getElementById('modalEventImage').src = event.image || '';
            document.getElementById('eventModal').style.display = 'block';
        }
    </script>
</body>
</html>
