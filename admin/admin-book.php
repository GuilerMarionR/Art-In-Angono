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

// Get the logged-in username
$username = $_SESSION['username'];

// Function to fetch guests for the current day
function fetchGuests($conn, $username, $date) {
    $sql = "SELECT * FROM clientbookings
            WHERE museumName = ? AND appointmentDate = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $date);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to fetch guest history (previous day)
function fetchGuestHistory($conn, $username, $date) {
    $sql = "SELECT * FROM clientbookings
            WHERE museumName = ? AND appointmentDate < ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $date);
    $stmt->execute();
    return $stmt->get_result();
}

// Function to fetch future bookings (greater than the current date)
function fetchFutureBookings($conn, $username, $date) {
    $sql = "SELECT * FROM clientbookings
            WHERE museumName = ? AND appointmentDate > ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $date);
    $stmt->execute();
    return $stmt->get_result();
}

function insertClosedTime($conn, $username, $date, $startTime, $endTime, $reason = null) {
    $sql = "INSERT INTO closed_times (museumName, date, startTime, endTime, reason) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $date, $startTime, $endTime, $reason);

    if ($stmt->execute()) {
        return true;
    } else {
        // If it fails, print the error to troubleshoot
        echo "Error: " . $stmt->error;
        return false;
    }
}

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateStatus'])) {
    $bookingID = $_POST['bookingID'];
    $newStatus = trim($_POST['updateStatus']); // This will be the new status value from the form

    // Check if the new status is valid
    if (empty($newStatus)) {
        die("Error: Status is required.");
    }

    // Update the status in the database
    $stmt = $conn->prepare("UPDATE clientbookings SET Status = ? WHERE bookingID = ?");
    $stmt->bind_param("si", $newStatus, $bookingID); // Use $newStatus here to update the status
    $stmt->execute();

    // Optionally: Include the email sending logic if status change is significant
    if (in_array($newStatus, ['Approved', 'Cancelled', 'Shown', 'Not Shown'])) {
        include 'send-book-email.php'; // Make sure send-book-email.php handles the email logic based on $newStatus
    }

    // Redirect or reload the page after the update
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch current day bookings
$date = date('Y-m-d'); // Current date
$guestListResult = fetchGuests($conn, $username, $date);
$totalGuests = $guestListResult->num_rows;

// Fetch guest history (previous day)
$previousDate = date('Y-m-d', strtotime('-1 day'));
$guestHistoryResult = fetchGuestHistory($conn, $username, $previousDate);
$totalHistory = $guestHistoryResult->num_rows;

// Fetch future bookings (greater than the current date)
$futureBookingsResult = fetchFutureBookings($conn, $username, $date);
$totalFutureBookings = $futureBookingsResult->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Lists</title>
    <link rel="stylesheet" href="../css/admin-book.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>
<style>
    /* CSS for consistent button size */
.icon-btn {
    width: 50px;    /* Adjust width as needed */
    height: 50px;   /* Adjust height as needed */
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5em; /* Adjust icon size */
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease; /* Smooth hover effect */
}

.icon-btn:hover {
    background-color: #e0e0e0; /* Slightly darker gray on hover */
}

.icon-btn[disabled] {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Tooltip adjustment (if needed) */
[data-bs-toggle="tooltip"] {
    cursor: pointer;
}

    .tab-content {
        margin: 10 auto;
        margin-left: 80px;
        max-width: 80%; /* Adjust width as needed */
    }
    .edit{
        margin-left: 600px;
        margin-top: 80px;
    }
       .thead-red {
        background-color: #C1121F;
        color: white;
    }
    .btn-secondary {
    background-color: #c0c0c0;
    color: #ffffff;
    cursor: not-allowed;
}

    /* White background for table body */
    .tbody-white {
        background-color: white;
    }

    /* Optional: Style for table rows on hover (if needed) */
    .table-striped tbody tr:hover {
        background-color: #f5f5f5;
    }
</style>
<body>

<?php include '../includes/navigation-admin.php'; ?>
<div class="museum-background"></div>

    <div class="button-group text-center my-3">
        <button  onclick="showSection('guest-list'); event.preventDefault();">Guest List</button>
        <button  onclick="showSection('guest-history'); event.preventDefault();">Guest History</button>
        <button onclick="showSection('pending-list'); event.preventDefault();">Pending List</button>
          <!-- Export to PDF Button -->
          <button type="button" id="exportToPDF" onclick="window.open('export_to_pdf.php', '_blank');">Export to PDF</button>
          <a href="date-time.php"><button>Edit Date & Time</button></a>
     </div>

    <!-- Guest List Section -->
<div class="tab-content" id="guest-list" style="display: none;">
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Age</th>
                <th>Contact Number</th>
                <th>Number of Guests</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($totalGuests > 0): ?>
                <?php while ($row = $guestListResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['firstName']); ?></td>
                        <td><?php echo htmlspecialchars($row['middleName']); ?></td>
                        <td><?php echo htmlspecialchars($row['lastName']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['age']); ?></td>
                        <td><?php echo htmlspecialchars($row['contactNumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['numberOfGuests']); ?></td>
                        <td><?php echo htmlspecialchars($row['appointmentDate']); ?></td>
                        <td>
                            <?php 
                                // Format start time to 12-hour format with AM/PM
                                $formattedStartTime = date('g:i A', strtotime($row['startTime']));

                                // Format end time to 12-hour format with AM/PM
                                $formattedEndTime = date('g:i A', strtotime($row['endTime']));

                                // Display the formatted start and end time
                                echo htmlspecialchars($formattedStartTime . ' - ' . $formattedEndTime); 
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['Status']); ?></td>
                        <td>
                            <!-- Buttons Row -->
                            <div style="display: flex; gap: 5px;">

                                      <!-- Shown Button (Blue) -->
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="bookingID" value="<?php echo $row['bookingID']; ?>">
                                    <input type="hidden" name="updateStatus" value="Shown">
                                    <button type="submit" class="icon-btn btn-primary" 
                                            title="Shown" 
                                            <?php echo ($row['Status'] == 'Shown' || $row['Status'] == 'Cancelled' || $row['Status'] == ' Not Shown') ? 'disabled' : ''; ?>>
                                        <i class="fas fa-eye"></i> <!-- Font Awesome eye icon for 'Shown' -->
                                    </button>
                                </form>

                                <!-- Not Shown Button (Yellow) -->
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="bookingID" value="<?php echo $row['bookingID']; ?>">
                                    <input type="hidden" name="updateStatus" value="Not Shown">
                                    <button type="submit" class="icon-btn btn-warning" 
                                            title="Not Shown" 
                                            <?php echo ($row['Status'] == 'Not Shown' || $row['Status'] == 'Cancelled' || $row['Status'] == 'Shown') ? 'disabled' : ''; ?>>
                                        <i class="fas fa-eye-slash"></i> <!-- Font Awesome eye-slash icon for 'Not Shown' -->
                                    </button>
                                </form>

                                                </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12">No guests for today</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<!-- Guest History Section -->
<div class="tab-content" id="guest-history" style="display: none;">
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Age</th>
                <th>Contact Number</th>
                <th>Number of Guests</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($totalHistory > 0): ?>
                <?php while ($row = $guestHistoryResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['firstName'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['middleName'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['lastName'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['address'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['age'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['contactNumber'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['numberOfGuests'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['appointmentDate'] ?? 'N/A'); ?></td>
                        <td>
                            <?php 
                                // Format start time to 12-hour format with AM/PM
                                $formattedStartTime = date('g:i A', strtotime($row['startTime']));

                                // Format end time to 12-hour format with AM/PM
                                $formattedEndTime = date('g:i A', strtotime($row['endTime']));

                                // Display the formatted start and end time
                                echo htmlspecialchars($formattedStartTime . ' - ' . $formattedEndTime); 
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['Status'] ?? 'N/A'); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11">No guest history found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pending List Section -->
<div class="tab-content" id="pending-list" style="display: none; ">
    <table class="table table-striped table-bordered" style="margin-left: 150px; margin-top: 50px;">
        <thead class="thead-red">
            <tr>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Age</th>
                <th>Contact Number</th>
                <th>Number of Guests</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody class="tbody-white">
            <?php if ($totalFutureBookings > 0): ?>
                <?php while ($row = $futureBookingsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['firstName'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['middleName'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['lastName'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['address'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['age'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['contactNumber'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['numberOfGuests'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['appointmentDate'] ?? 'N/A'); ?></td>
                        <td>
                            <?php 
                                // Format start time to 12-hour format with AM/PM
                                $formattedStartTime = date('g:i A', strtotime($row['startTime']));

                                // Format end time to 12-hour format with AM/PM
                                $formattedEndTime = date('g:i A', strtotime($row['endTime']));

                                // Display the formatted start and end time
                                echo htmlspecialchars($formattedStartTime . ' - ' . $formattedEndTime); 
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['Status'] ?? 'N/A'); ?></td>
                        <td>
                        <div style="display: flex; gap: 5px;">
                                    <!-- Approved Button (Green) -->
        <form action="" method="POST" style="display:inline;">
            <input type="hidden" name="bookingID" value="<?php echo $row['bookingID']; ?>">
            <input type="hidden" name="updateStatus" value="Approved">
            <button type="submit" class="icon-btn btn-success" 
                    title="Approved" 
                    <?php echo ($row['Status'] == 'Approved' || $row['Status'] == 'Cancelled') ? 'disabled' : ''; ?>>
                <i class="fas fa-check"></i> <!-- Font Awesome check icon for 'Approved' -->
            </button>
        </form>

        <!-- Cancelled Button (Red) -->
        <form action="" method="POST" style="display:inline;">
            <input type="hidden" name="bookingID" value="<?php echo $row['bookingID']; ?>">
            <input type="hidden" name="updateStatus" value="Cancelled">
            <button type="submit" class="icon-btn btn-danger" 
                    title="Cancelled" 
                    <?php echo ($row['Status'] == 'Cancelled' || $row['Status'] == 'Approved' ) ? 'disabled' : ''; ?>>
                <i class="fas fa-times"></i> <!-- Font Awesome times icon for 'Cancelled' -->
            </button>
        </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12">No future bookings</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<!-- Edit Calendar Section -->
<div class="tab-content" id="edit-calendar" style="display: none;">
    
</div>

<script>
    function showSection(sectionId) {
        const sections = document.querySelectorAll('.tab-content');
        sections.forEach(section => section.style.display = 'none'); // Hide all sections

        const selectedSection = document.getElementById(sectionId);
        selectedSection.style.display = 'block'; // Show the selected section
    }
</script>

</body>
</html>
