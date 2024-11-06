<?php 
session_start(); // Start the session

// Include your database connection
include '../includes/db_connections.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../logins/login.php");
    exit();
}

// Get the logged-in user's username
$username = $_SESSION['username'];

// Check if a delete request has been made
if (isset($_GET['id'])) {
    $artworkID = intval($_GET['id']); // Sanitize input

    // Prepare and execute delete query
    $stmt = $conn->prepare("DELETE FROM artworks WHERE artworkID = ?");
    $stmt->bind_param("i", $artworkID);

    if ($stmt->execute()) {
        $deleteMessage = "Artwork deleted successfully.";
    } else {
        $deleteMessage = "Error deleting artwork: " . $conn->error;
    }

    $stmt->close();
}

// Pagination variables
$limit = 3; // Number of artworks per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page number
$offset = ($page - 1) * $limit; // Calculate offset

// Fetch total artworks count for pagination
$totalQuery = "SELECT COUNT(*) as total FROM artworks";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalArtworks = $totalRow['total'];
$totalPages = ceil($totalArtworks / $limit); // Calculate total pages

// Fetch artworks from the database with limit and offset, filtering by the logged-in user's username
$query = "SELECT artistName, title, artworkID, imagePath FROM artworks WHERE museumName = ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sii", $username, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - ADMIN</title>
    <link rel="stylesheet" href="../css/admin-my-art.css">
    <style>
        .artwork-display {
            padding: 20px;
            background-color: #ffffff; /* White background for the artwork display */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
            max-width: 1200px; /* Limit the width of the container */
            margin: 20px auto; /* Center the container */
        }

        .button-group {
            padding-bottom: 30px;
            display: flex;
            justify-content: center; /* Center the buttons */
        }

        .artwork-row {
            display: grid; /* Use grid layout for artworks */
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Responsive grid */
            gap: 20px; /* Space between items */
        }

        .artwork-container {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            background-color: #ffffff; /* White background for artwork container */
            text-align: center; /* Center align text and image */
            transition: transform 0.2s; /* Smooth transition on hover */
        }

        .artwork-container:hover {
            transform: scale(1.02); /* Slight zoom on hover */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); /* Deeper shadow on hover */
        }

        .artwork-container img {
            max-width: 100%; /* Make the image responsive */
            height: auto; /* Maintain aspect ratio */
            border-radius: 5px; /* Rounded corners for images */
        }

        .pagination {
            text-align: center; /* Center the pagination links */
            margin-top: 20px; /* Space above pagination */
        }

        .pagination a {
            margin: 0 5px; /* Space between pagination links */
            padding: 5px 10px; /* Padding for pagination links */
            background-color: #007bff; /* Primary color */
            color: white; /* Text color */
            text-decoration: none; /* Remove underline */
            border-radius: 5px; /* Rounded corners */
        }

        .pagination a:hover {
            background-color: #0056b3; /* Darker shade on hover */
        }

        /* Button styles */
        .update-button {
            background-color: red; /* Red background for Update button */
            color: white; /* White text */
            border: none; /* Remove border */
            padding: 10px 15px; /* Button padding */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            margin-right: 5px; /* Space between buttons */
        }

        .delete-button {
            background-color: gray; /* Gray background for Delete button */
            color: white; /* White text */
            border: none; /* Remove border */
            padding: 10px 15px; /* Button padding */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
        }

        .button-container {
            display: flex; /* Use flexbox to align buttons in a line */
            justify-content: center; /* Center the buttons */
            margin-top: 10px; /* Space above buttons */
        }
        a {
            text-decoration: none; /* Remove underline from links */
            color: inherit; /* Inherit color from parent elements */
        }
    </style>
</head>
<body>
    
    <?php include '../includes/navigation-admin.php'; ?>
    <div class="admin-art-background">
    <div class="artwork-display"> 
        <div class="admin-art-content">
            <div class="button-group">
                <button onclick="window.location.href='admin-my-art.php'">My Artworks</button>
                <button onclick="window.location.href='admin-add-art.php'">Add Artwork</button>
            </div>

            <?php if (isset($deleteMessage)): ?>
                <p><?php echo htmlspecialchars($deleteMessage); ?></p>
            <?php endif; ?>

            <div class="artwork-row">
                <?php
                // Check if artworks were found
                if (mysqli_num_rows($result) > 0) {
                    // Output data for each artwork
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<div class="artwork-container" id="artwork-' . htmlspecialchars($row['artworkID']) . '">';
                        // Link to artwork preview page
                        echo '<a href="admin-art-previews.php?id=' . htmlspecialchars($row['artworkID']) . '">';
                        echo '<img src="' . htmlspecialchars($row['imagePath']) . '" alt="' . htmlspecialchars($row['title']) . '">';
                        echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                        echo '</a>'; // Close link

                        // Button container for alignment
                        echo '<div class="button-container">';
                        // Update button
                        echo '<button class="update-button" onclick="window.location.href=\'admin-edit-art.php?id=' . htmlspecialchars($row['artworkID']) . '\'">Update</button>';
                        // Delete button
                        echo '<button class="delete-button" onclick="confirmDelete(' . htmlspecialchars($row['artworkID']) . ')">Delete</button>';
                        echo '</div>'; // Close button-container
                        echo '</div>'; // Close artwork-container
                    }
                } else {
                    echo '<p>No artworks found.</p>';
                }

                // Free the result set
                mysqli_free_result($result);
                ?>
            </div>

            <!-- Pagination Controls -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="admin-my-art.php?page=<?php echo $page - 1; ?>">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="admin-my-art.php?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'style="font-weight:bold;"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="admin-my-art.php?page=<?php echo $page + 1; ?>">Next</a>
                <?php endif; ?>
            </div>
    
            <script>
                function confirmDelete(artworkID) {
                    if (confirm("Are you sure you want to delete this artwork?")) {
                        window.location.href = 'admin-my-art.php?id=' + artworkID; // Redirect to the same page with the delete request
                    }
                }
            </script>
    
        </div>
    </div>
    </div>
    
</body>
</html>
