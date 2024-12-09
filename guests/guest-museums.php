<?php
// Start the session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - Museums</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

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

<!-- Background Section -->
<div class="museum-background"></div>
<div class="museum-content"></div>

<!-- Main Content -->
<main>

    <!-- Gallery Section -->
    <div class="gallery">
        <?php
        // Connect to the database
        $conn = new mysqli('localhost', 'root', '', 'art_in_angono_db');

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Define pagination variables
        $limit = 6; // Number of items per page
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Handle search query
        $search = $_GET['search'] ?? '';
        $search_sql = $search ? "WHERE name LIKE '%" . $conn->real_escape_string($search) . "%'" : '';

        // Fetch data from the museums table with pagination and search
        $sql = "SELECT id, name, address, image_url FROM museums $search_sql LIMIT $limit OFFSET $offset";
        $result = $conn->query($sql);

        // Check if there are results
        if ($result->num_rows > 0) {
            // Loop through and display each museum as a card
            while ($row = $result->fetch_assoc()) {
                // Fetch view count for the current museum
                $museum_id = $row['id'];
                $view_count_sql = "SELECT views FROM museums WHERE id = $museum_id";
                $view_count_result = $conn->query($view_count_sql);
                $view_count = 0;
                if ($view_count_result->num_rows > 0) {
                    $view_data = $view_count_result->fetch_assoc();
                    $view_count = $view_data['views'];
                }

                // Display the museum card with view count below address
                echo '<a href="guest-museum-info.php?id=' . $row['id'] . '" style="text-decoration: none; color: inherit;">
                    <div class="card">
                        <img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">
                        <h3>' . htmlspecialchars($row['name']) . '</h3>
                        <p>' . htmlspecialchars($row['address']) . '</p>
                        <p><strong>Views:</strong> ' . $view_count . '</p> <!-- Displaying view count below address -->
                    </div>
                </a>';
            }
        } else {
            echo "<p>No museums available at this time.</p>";
        }

        // Get total number of museums for pagination
        $total_sql = "SELECT COUNT(*) as total FROM museums $search_sql";
        $total_result = $conn->query($total_sql);
        $total_museums = $total_result->fetch_assoc()['total'];
        $total_pages = ceil($total_museums / $limit);

        // Close the database connection
        $conn->close();
        ?>
    </div>

    <!-- Pagination Controls -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">&laquo; Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="<?php if ($i == $page) echo 'active'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>

</main>
</body>
</html>
