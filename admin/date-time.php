<?php
session_start();

// Include database connection
include '../includes/db_connections.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../logins/login.php");
    exit();
}

// Get the logged-in user's username (acting as the museumName)
$username = $_SESSION['username']; // This acts as the museumName

// Insert closed time
if (isset($_POST['add_closed_time'])) {
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $date = $_POST['date'];
    $reason = $_POST['reason'];

    $date = date('Y-m-d', strtotime($date));

    // Insert into the database using prepared statement
    $stmt = $conn->prepare("INSERT INTO closed_times (startTime, endTime, date, reason, museumName) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $startTime, $endTime, $date, $reason, $username);
    if (!$stmt->execute()) {
        echo "Error adding closed time: " . $stmt->error;
    }
    $stmt->close();
}
$stmt = $conn->prepare("SELECT * FROM closed_dates WHERE museumName = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$closed_dates_result = $stmt->get_result();

// Prepare closed dates for JavaScript
$closed_dates_array = [];
while ($row = $closed_dates_result->fetch_assoc()) {
    $closed_dates_array[] = date('Y-m-d', strtotime($row['closed_date']));
}
$closed_dates_json = json_encode($closed_dates_array);

// Fetch closed times for the logged-in user (museum)
$stmt = $conn->prepare("SELECT * FROM closed_times WHERE museumName = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$closed_times_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule</title>
    <link rel="stylesheet" href="../css/admin-book.css">
    <link rel="stylesheet" href="../css/date-time.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<style>
    /* Style for graying out the disabled closed dates */
    .ui-state-disabled.closed-date {
        background-color: #ddd !important;
        color: #bbb !important;
        cursor: not-allowed !important;
    }
    /* Prevent interaction with the disabled dates */
    .ui-state-disabled {
        pointer-events: none !important;
    }
</style>
<body>
<?php include '../includes/navigation-admin.php'; ?>
<div class="museum-background"></div>
<div class="button-group text-center my-3" style="margin-left:725px;">
    <a href="admin-book.php">
        <button>Guest List</button>
    </a>
</div>
<h1>Manage Closed Dates and Times</h1>

<div class="form-container" style="margin-left:550px;">
    <!-- Closed Dates Form -->
    <form method="POST" action="">
        <h3>Add Closed Date</h3>
        <label for="closed_date">Closed Date:</label>
        <input type="text" id="closed_date" name="closed_date" required>
        <br>
        <label for="reason">Reason:</label>
        <input type="text" name="reason" required>
        <br>
        <button type="submit" name="add_closed_date">Add Closed Date</button>
    </form>
</div>
<?php include 'closed_dates.php'; ?>

<div class="form-container" style="margin-left:550px;">
    <form method="POST" action="">
        <h3>Add Closed Time</h3>
        <label for="startTime">Start Time:</label>
        <select name="startTime" required>
            <?php
            for ($i = 8; $i <= 16; $i++) {
                $hour = str_pad($i, 2, "0", STR_PAD_LEFT);
                $time24 = $hour . ":00";
                $time12 = date("h:i A", strtotime($time24));
                echo "<option value='$time24'>$time12 ($time24)</option>";
            }
            ?>
        </select>
        <br>

        <label for="endTime">End Time:</label>
        <select name="endTime" required>
            <?php
            for ($i = 8; $i <= 16; $i++) {
                $hour = str_pad($i, 2, "0", STR_PAD_LEFT);
                $time24 = $hour . ":00";
                $time12 = date("h:i A", strtotime($time24));
                echo "<option value='$time24'>$time12 ($time24)</option>";
            }
            ?>
        </select>
        <br>

        <label for="date">Date:</label>
        <input type="text" id="closed_time_date" name="date" required>
        <br>

        <label for="reason">Reason:</label>
        <input type="text" name="reason">
        <br>

        <button type="submit" name="add_closed_time">Add Closed Time</button>
    </form>
</div>

<!-- Display Closed Times -->
<h3>Closed Times</h3>
<table border="1">
    <thead>
        <tr>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Date</th>
            <th>Reason</th>
            <th>Museum Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $closed_times_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['startTime']; ?></td>
                <td><?php echo $row['endTime']; ?></td>
                <td><?php echo $row['date']; ?></td>
                <td><?php echo $row['reason']; ?></td>
                <td><?php echo $row['museumName']; ?></td>
                <td>
                    <a href="?delete_closed_time=<?php echo $row['id']; ?>">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
    // Fetch closed dates from the database and disable them on the calendar
    const closedDates = <?php echo $closed_dates_json; ?>;

    $(document).ready(function () {
        $('#closed_date, #closed_time_date').datepicker({
            dateFormat: 'yy-mm-dd',
            beforeShowDay: function (date) {
                const string = $.datepicker.formatDate('yy-mm-dd', date);
                const today = new Date();
                const formattedToday = today.toISOString().split('T')[0]; 

                // Disable past dates and closed dates
                if (string < formattedToday || closedDates.includes(string)) {
                    return [false, 'ui-state-disabled closed-date', 'Closed'];
                }
                return [true, '', ''];
            },
            onSelect: function (dateText) {
                if (closedDates.includes(dateText)) {
                    alert('This date is closed and cannot be selected!');
                    $(this).val('');
                }
            }
        });
    });
</script>


    <?php

    // Delete closed time
    if (isset($_GET['delete_closed_time'])) {
        $delete_id = $_GET['delete_closed_time'];
        $delete_sql = "DELETE FROM closed_times WHERE id = $delete_id";
        $delete_result = mysqli_query($conn, $delete_sql);
        if ($delete_result) {
            // echo "Closed time deleted successfully!";
            header("Location: {$_SERVER['PHP_SELF']}");
        } else {
            echo "Error deleting closed time: " . mysqli_error($conn);
        }
    }
    ?>
</body>
</html>
