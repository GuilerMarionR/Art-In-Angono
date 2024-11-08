<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include '../includes/navigation-guest.php'; ?>

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
                    echo '<a href="guest-museum-info.php?id=' . $row['id'] . '" style="text-decoration: none; color: inherit;">
                        <div class="card">
                            <img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">
                            <h3>' . htmlspecialchars($row['name']) . '</h3>
                            <p>' . htmlspecialchars($row['address']) . '</p>
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
