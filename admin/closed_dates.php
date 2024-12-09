<?php
// Include database connection
include '../includes/db_connections.php';

// Get the logged-in user's username (acting as the museumName)
$username = $_SESSION['username']; // This acts as the museumName

// Insert closed date
if (isset($_POST['add_closed_date'])) {
    $closed_date = $_POST['closed_date'];
    $reason = $_POST['reason'];

    // Check if the date is empty or invalid
    if (empty($closed_date) || $closed_date === '0000-00-00') {
        echo "Invalid or empty date entered.";
    } else {
        // Format the date to YYYY-MM-DD
        $closed_date = date('Y-m-d', strtotime($closed_date));

        // Insert into the database
        $sql = "INSERT INTO closed_dates (closed_date, reason, museumName) 
                VALUES ('$closed_date', '$reason', '$username')";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            echo "Error adding closed date: " . mysqli_error($conn);
        }
    }
}

// Fetch closed dates for the logged-in user (museum)
$closed_dates_sql = "SELECT * FROM closed_dates WHERE museumName = '$username'";
$closed_dates_result = mysqli_query($conn, $closed_dates_sql);

?>
<!-- Display Closed Dates -->
<h3>Closed Dates</h3>
<table border="1">
    <tr>
        <th>Closed Date</th>
        <th>Reason</th>
        <th>Museum Name</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($closed_dates_result)): ?>
    <tr>
        <td><?php echo $row['closed_date']; ?></td>
        <td><?php echo $row['reason']; ?></td>
        <td><?php echo $row['museumName']; ?></td>
        <td><a href="?delete_closed_date=<?php echo $row['id']; ?>">Delete</a></td>
    </tr>
    <?php endwhile; ?>
</table>

<?php
// Delete closed date
if (isset($_GET['delete_closed_date'])) {
    $delete_id = $_GET['delete_closed_date'];
    $delete_sql = "DELETE FROM closed_dates WHERE id = $delete_id";
    $delete_result = mysqli_query($conn, $delete_sql);
    if ($delete_result) {
        header("Location: {$_SERVER['PHP_SELF']}");
    } else {
        echo "Error deleting closed date: " . mysqli_error($conn);
    }
}
?>
