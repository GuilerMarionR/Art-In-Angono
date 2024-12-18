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

    /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f0f0f0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    font-size: 24px;
    text-align: center;
    margin-top: 20px;
}

/* Make table scrollable on smaller screens */
.table-wrapper {
    overflow-x: auto; /* Enable horizontal scroll */
    -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
    margin-top: 20px;
}

/* Style the table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
}
td{
    background-color: white;
}

/* Sticky header */
thead {
    position: sticky;
    top: 0;
    background-color: #C1121F;
    color: white;
    z-index: 1;
}

/* Table rows on hover */
.table-striped tbody tr:hover {
    background-color: #f5f5f5;
}

/* Style for buttons */
.cancel-btn {
    background-color: #C1121F;
    color: white;
    border: none;
    padding: 8px;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.cancel-btn i {
    font-size: 18px;
}

.cancel-btn.cancel-btn-disabled {
    background-color: #d3d3d3;
    color: #888;
    border: 1px solid #ccc;
    cursor: not-allowed;
}

.cancel-btn.cancel-btn-disabled i {
    color: #888;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
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
}

.pagination a:hover {
    background-color: #C1121F;
    color: white;
}

.pagination .active {
    background-color: #C1121F;
    color: white;
}

/* Responsive Styles for smaller screens */
@media (max-width: 768px) {
    /* Stack buttons for mobile */
    .cancel-btn {
        width: 100%; /* Full width on small screens */
        margin-top: 10px;
    }

    /* Adjust the font size and padding for smaller screens */
    h1 {
        font-size: 20px;
    }

    /* Stack table rows and allow horizontal scrolling */
    .table-wrapper {
        overflow-x: auto;
    }

    table {
        font-size: 12px; /* Smaller text for table */
    }

     td {
        padding: 8px;
        background-color: white;
    }

    /* Pagination links adjustment */
    .pagination a {
        padding: 6px 12px;
        font-size: 12px;
    }
}
/* Container for the buttons */
.button-container {
    display: flex;
    justify-content: center; /* Center the buttons horizontally */
    align-items: center; /* Align the buttons vertically */
    margin-top: 20px; /* Add some space above the buttons */
}

/* Styling for the buttons */
.button-container a {
    margin: 0 10px; /* Add space between the buttons horizontally */
}

@media (max-width: 480px) {
    /* Further adjustments for very small screens */
    h1 {
        font-size: 18px;
    }

    .cancel-btn {
        font-size: 14px; /* Smaller buttons on very small screens */
    }
    .button-container {
        flex-direction: column; /* Stack the buttons vertically on mobile */
    }

    .button-container a button {
        width: 100%; /* Full width buttons on mobile */
        padding: 12px; /* Larger padding for easier clicking */
        font-size: 18px; /* Adjust font size for mobile */
    }
}


</style>
<body>
<?php include '../includes/navigation-loggedin.php'; // Include navbar for logged-in users ?>
<div class="museum-background"></div>
<div class="button-container">
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
