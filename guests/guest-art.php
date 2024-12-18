<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - Arts</title>
    <link rel="stylesheet" href="../css/style.css">
<?php
session_start();
// Database connection
include '../includes/db_connections.php';

// Search functionality
$searchTerm = isset($_GET['search']) ? $_GET['search'] : ''; // Use GET for search
$sql = "SELECT artworkID, title, artistName, imagePath FROM artworks WHERE title LIKE ? LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$limit = 3; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$searchParam = '%' . $searchTerm . '%';
$stmt->bind_param('sii', $searchParam, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Count total artworks for pagination
$totalSql = "SELECT COUNT(*) FROM artworks WHERE title LIKE ?";
$totalStmt = $conn->prepare($totalSql);
$totalStmt->bind_param('s', $searchParam);
$totalStmt->execute();
$totalStmt->bind_result($totalArtworks);
$totalStmt->fetch();
$totalPages = ceil($totalArtworks / $limit);
$totalStmt->close();


// Check if the user is logged in by checking for the session username
if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
    // If logged in, include the logged-in navbar
    include '../includes/navigation-loggedin.php';
} else {
    // If not logged in, include the guest navbar
    include '../includes/navigation-guest.php';
}
?>


<!-- Container for Search Form and Artwork Gallery -->
<div class="museum-background"></div>
<div class="container">
    <!-- Search Form -->
    <form method="GET" action="" class="search-form">
        <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search artworks..." required oninput="this.form.submit()">
    </form>

    <div class='artwork-gallery'> <!-- Main gallery container -->
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php $artworkID = htmlspecialchars($row['artworkID']); ?>
            <a href='guest-art-info.php?id=<?php echo urlencode($artworkID); ?>' class='artwork-item'> <!-- Artwork box container with link -->
                <img src='<?php echo htmlspecialchars($row['imagePath']); ?>' alt='<?php echo htmlspecialchars($row['title']); ?>' />
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p>Artist: <?php echo htmlspecialchars($row['artistName']); ?></p> <!-- Display artist name -->
            </a>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No artworks found.</p>
    <?php endif; ?>
</div> <!-- Close the gallery -->
    <!-- Pagination -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($searchTerm); ?>">Previous</a>
    <?php endif; ?>
    <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($searchTerm); ?>">Next</a>
    <?php endif; ?>
</div>

</div> <!-- Close the container -->

<?php
$conn->close();
?>

<style>

.search-form {
    display: flex; /* Use flexbox to align items */
    justify-content: flex-end; /* Align items to the right */
    margin-bottom: 20px; /* Space below the search form */
}

.search-form input {
    padding: 10px; /* Padding inside the input */
    border: 1px solid #ccc; /* Border for the input */
    border-radius: 4px; /* Rounded corners */
    width: 250px; /* Fixed width for the input */
}

.artwork-gallery {
    display: flex;
    justify-content: space-between; /* Distributes items evenly */
    flex-wrap: nowrap; /* Prevents wrapping to the next line */
    margin-bottom: 20px; /* Space below the gallery */
}

.artwork-item {
    flex: 1; /* Each item takes equal space */
    margin: 5px; /* Space between items */
    background-color: #fff; /* Light background for artwork items */
    border: 1px solid #ddd; /* Border around the box */
    border-radius: 8px; /* Rounded corners */
    text-align: center; /* Center text */
    text-decoration: none; /* Remove underline from links */
    color: inherit; /* Inherit text color */
    overflow: hidden; /* Prevent overflow */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    transition: transform 0.3s; /* Transition for scale effect */
    max-width: calc(33.33% - 10px); /* Ensure three items fit in one line with margins */
}

.artwork-item img {
    width: 100%; /* Full width */
    height: auto; /* Maintain aspect ratio */
}

.artwork-item h3, .artwork-item p {
    margin: 5px 0; /* Space between title and description */
}

.artwork-item:hover {
    transform: scale(1.05); /* Slight zoom on hover */
}

.pagination {
    margin-top: 20px; /* Space above pagination */
    text-align: center; /* Center pagination */
}

.pagination a {
    margin: 0 5px; /* Space between pagination links */
    text-decoration: none; /* No underline */
    color: #007BFF; /* Link color */
}

.pagination span {
    margin: 0 5px; /* Space around page info */
}
/* General Container Styles */
.container {
    max-width: 1100px; /* Maximum width of the container */
    margin: 0 auto; /* Center the container */
    padding: 20px; /* Padding around the container */
    border: 1px solid #ddd; /* Border for the box */
    border-radius: 8px; /* Rounded corners */
    background-color: #f8f8f8; /* Light background color */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}

/* Search Form Styling */
.search-form {
    display: flex; /* Use flexbox to align items */
    justify-content: flex-end; /* Align items to the right */
    margin-bottom: 20px; /* Space below the search form */
}

.search-form input {
    padding: 10px; /* Padding inside the input */
    border: 1px solid #ccc; /* Border for the input */
    border-radius: 4px; /* Rounded corners */
    width: 250px; /* Fixed width for the input */
}

/* Artwork Gallery Styles */
.artwork-gallery {
    display: flex;
    justify-content: space-between; /* Distributes items evenly */
    flex-wrap: wrap; /* Allows items to wrap on smaller screens */
    margin-bottom: 20px; /* Space below the gallery */
}

/* Artwork Item Styling */
.artwork-item {
    flex: 1; /* Each item takes equal space */
    margin: 5px; /* Space between items */
    background-color: #fff; /* Light background for artwork items */
    border: 1px solid #ddd; /* Border around the box */
    border-radius: 8px; /* Rounded corners */
    text-align: center; /* Center text */
    text-decoration: none; /* Remove underline from links */
    color: inherit; /* Inherit text color */
    overflow: hidden; /* Prevent overflow */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    transition: transform 0.3s; /* Transition for scale effect */
    max-width: calc(33.33% - 10px); /* Ensure three items fit in one line with margins */
}

.artwork-item img {
    width: 100%; /* Full width */
    height: auto; /* Maintain aspect ratio */
}

.artwork-item h3, .artwork-item p {
    margin: 5px 0; /* Space between title and description */
}

.artwork-item:hover {
    transform: scale(1.05); /* Slight zoom on hover */
}

/* Pagination Styles */
.pagination {
    margin-top: 20px; /* Space above pagination */
    text-align: center; /* Center pagination */
}

.pagination a {
    margin: 0 5px; /* Space between pagination links */
    text-decoration: none; /* No underline */
    color: #007BFF; /* Link color */
}

.pagination span {
    margin: 0 5px; /* Space around page info */
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
    /* Adjust search form for mobile */
    .container{
        max-width: 300px;
    }
    .search-form {
        justify-content: center; /* Center search bar on mobile */
    }

    .search-form input {
        width: 100%; /* Make search input full-width on mobile */
        max-width: 400px; /* Limit max-width for larger mobile screens */
    }

    /* Adjust artwork gallery for mobile */
    .artwork-gallery {
        flex-direction: column; /* Stack the gallery items vertically on small screens */
        align-items: center; /* Center the items */
    }

    .artwork-item {
        max-width: 90%; /* Make artwork items take up most of the screen width */
        margin: 10px 0; /* Add space between items */
    }

    /* Adjust pagination for mobile */
    .pagination {
        display: flex;
        flex-direction: column; /* Stack pagination items vertically */
        align-items: center;
    }

    .pagination a {
        margin: 5px 0; /* Space between pagination links */
    }
}

</style>
