<?php
// Start the session
session_start();

// Check if the user is logged in by checking for the session username
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    // If not logged in, redirect to the login page
    header('Location: ../logins/login.php');
    exit(); // Make sure no further code is executed after the redirect
}

// Include database connection
include '../includes/db_connections.php';

// Fetch booking history for the logged-in user with pagination
$username = $_SESSION['username'];

// Pagination setup
$perPage = 4; // Number of records to display per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get current page number from URL, default to 1 if not set
$offset = ($currentPage - 1) * $perPage; // Calculate the offset for LIMIT

// Get the total number of records
$totalRecordsQuery = "SELECT COUNT(*) AS total FROM clientbookings WHERE username = ?";
$stmt = $conn->prepare($totalRecordsQuery);
$stmt->bind_param("s", $username);
$stmt->execute();
$totalRecordsResult = $stmt->get_result();
$totalRecords = $totalRecordsResult->fetch_assoc()['total'];
$stmt->close();

// Cancel Booking if the cancel button is clicked
if (isset($_POST['cancelBookingID'])) {
    $bookingID = $_POST['cancelBookingID'];
    
    // Update the status of the booking to 'Cancelled' in the database
    $updateStatusQuery = "UPDATE clientbookings SET status = 'Cancelled' WHERE bookingID = ?";
    $stmt = $conn->prepare($updateStatusQuery);
    $stmt->bind_param("i", $bookingID);
    $stmt->execute();
    $stmt->close();

    // Return a success response
    echo json_encode(['status' => 'success']);
    exit();
}

// Fetch bookings for the current page with LIMIT and OFFSET
$sql = "SELECT bookingID, museumName, firstName, lastName, middleName, email, address, age, appointmentDate, startTime, endTime, status 
        FROM clientbookings WHERE username = ? LIMIT $perPage OFFSET $offset";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt->close();

$conn->close();

// Calculate total pages
$totalPages = ceil($totalRecords / $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art In Angono - Booking History</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>

    /* Optional: Style for the tabs (if used) */
    .tabs {
        position: sticky;
        top: 0;
        z-index: 1000;  /* Ensure the tabs stay above other content */
        background-color: #f1f1f1; /* Light background for tabs */
        padding: 10px;  /* Adds spacing around the tabs */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Shadow for tabs */
    }
    /* Fix table header to stay on top while scrolling */
    thead {
        position: sticky;
        top: 0; /* Stay at the top */
        background-color: #C1121F; /* Red background for header */
        color: white; /* White text color */
        z-index: 1; /* Ensure the header stays above the content */
    }

    /* Optional: Style for table rows on hover */
    .table-striped tbody tr:hover {
        background-color: #f5f5f5;
    }

    /* Add a white background for table rows to ensure clean appearance */
    .tbody-white {
        background-color: white;
    }

    /* Style the table itself */
    table {
        width: 100%; /* Full width of the container */
        border-collapse: collapse; /* Remove spaces between table borders */
        margin-top: 20px; /* Space above the table */
    }

    /* Table cell styling */
    th, td {
        padding: 10px; /* Padding inside cells */
        text-align: left; /* Align text to the left */
        border: 1px solid #ddd; /* Light gray border for cells */
    }

    .cancel-btn {
    background-color: #C1121F; /* Red background for cancel button */
    color: white; /* White text color */
    border: none; /* Remove default button border */
    padding: 8px; /* Adjust padding to fit the icon */
    cursor: pointer; /* Pointer cursor on hover */
    font-size: 16px; /* Adjust the font size */
    border-radius: 5px; /* Rounded corners */
    display: inline-flex; /* Allow icon and text to align nicely */
    align-items: center; /* Center the icon vertically */
    justify-content: center; /* Center the icon horizontally */
}

.cancel-btn i {
    font-size: 18px; /* Set icon size */
}

.cancel-btn.cancel-btn-disabled {
    background-color: #d3d3d3; /* Light gray background */
    color: #888; /* Dark gray text */
    border: 1px solid #ccc; /* Light gray border */
    cursor: not-allowed; /* Not allowed cursor */
}

.cancel-btn.cancel-btn-disabled i {
    color: #888; /* Change the icon color for the disabled button */
}

    /* Make the whole table scrollable (if needed) */
    .table-wrapper {
        max-height: 400px; /* Set the max height for the table */
        overflow-y: auto; /* Enable vertical scrolling */
    }

    /* Optional: Style for the tabs (if used) */
   /* Centering the tabs */
.tabs {
    display: flex;
    justify-content: center; /* Center align tabs */
    position: sticky;
    top: 0;
    z-index: 1000;
    background-color: #f1f1f1;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* Center the pagination links */
.pagination {
    display: inline-flex; /* Align pagination links in a row */
    justify-content: center; /* Center the links horizontally */
    margin-top: 20px;
}

.pagination a {
    margin: 0 5px;
    padding: 8px 16px;
    text-decoration: none;
    color: #C1121F;
    border: 1px solid #C1121F;
    border-radius: 5px;
    font-size: 14px;
    display: inline-flex;
    justify-content: center; /* Ensure the link text is centered */
    align-items: center; /* Vertically align the text */
}

.pagination a:hover {
    background-color: #C1121F;
    color: white;
}

.pagination .active {
    background-color: #C1121F;
    color: white;
}

</style>
<body>
<?php include '../includes/navigation-loggedin.php'; // Include navbar for logged-in users ?>
<div class="museum-background"></div>
<div style="margin-left:670px;">
    <a href="guest-book.php">
        <button>List of Museums</button>
    </a>
    <a href="guest-booking.php">
        <button>Booking History</button>
    </a>
</div>
    <h1>Your Booking History</h1>
    <div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Museum Name</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Age</th>
                <th>Appointment Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr class="tbody-white">
                    <td><?php echo $booking['bookingID']; ?></td>
                    <td><?php echo $booking['museumName']; ?></td>
                    <td><?php echo $booking['firstName'] . ' ' . $booking['middleName'] . ' ' . $booking['lastName']; ?></td>
                    <td><?php echo $booking['email']; ?></td>
                    <td><?php echo $booking['age']; ?></td>
                    <td><?php echo $booking['appointmentDate']; ?></td>
                    <td><?php echo $booking['status']; ?></td>
                    <td>
    <?php if ($booking['status'] == 'Pending'): ?>
        <!-- Use Font Awesome trash icon inside the button -->
        <button class="cancel-btn" data-booking-id="<?php echo $booking['bookingID']; ?>">
            <i class="fas fa-ban"></i> <!-- Font Awesome Trash Icon -->
        </button>
    <?php else: ?>
        <button class="cancel-btn cancel-btn-disabled" disabled>
            <i class="fas fa-ban"></i> <!-- Font Awesome Disabled/Not allowed Icon -->
        </button>
    <?php endif; ?>
</td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Centered Pagination -->
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" class="<?php echo ($currentPage == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>

    <script>
        $(document).on('click', '.cancel-btn', function() {
            var bookingID = $(this).data('booking-id');

            $.ajax({
                type: 'POST',
                url: 'guest-booking.php',
                data: { cancelBookingID: bookingID },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.status === 'success') {
                        alert('Booking cancelled successfully!');
                        location.reload(); // Reload the page to reflect the status change
                    }
                }
            });
        });
        $(document).on('click', '.cancel-btn', function () {
    const bookingID = $(this).data('booking-id');
    
    if (confirm('Are you sure you want to cancel this booking?')) {
        $.ajax({
            type: 'POST',
            url: 'cancel_booking.php', // The separate PHP file
            data: { bookingID: bookingID },
            success: function (response) {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    alert(result.message);
                    location.reload(); // Reload the page to show updated status
                } else {
                    alert(result.message);
                }
            },
            error: function () {
                alert('An error occurred while processing your request.');
            },
        });
    }
});

    </script>
</body>
</html>
