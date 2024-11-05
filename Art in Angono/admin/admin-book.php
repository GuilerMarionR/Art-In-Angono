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
function fetchGuests($conn, $username, $date, $limit, $offset) {
    $sql = "SELECT * FROM clientbookings
            WHERE museumName = '$username' AND appointmentDate = '$date'
            LIMIT $limit OFFSET $offset";
    return $conn->query($sql);
}

// Function to fetch guest history (previous day)
function fetchGuestHistory($conn, $username, $date, $limit, $offset) {
    $sql = "SELECT * FROM clientbookings
            WHERE museumName = '$username' AND appointmentDate < '$date'
            LIMIT $limit OFFSET $offset";
    return $conn->query($sql);
}

// Function to toggle a closed date
function toggleClosedDate($conn, $date) {
    // Check if the date already exists
    $stmt = $conn->prepare("SELECT * FROM closed_dates WHERE closed_date = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Date exists, so remove it
        $stmt = $conn->prepare("DELETE FROM closed_dates WHERE closed_date = ?");
        $stmt->bind_param("s", $date);
        return $stmt->execute();
    } else {
        // Date does not exist, so add it
        $stmt = $conn->prepare("INSERT INTO closed_dates (closed_date) VALUES (?)");
        $stmt->bind_param("s", $date);
        return $stmt->execute();
    }
}

// Function to fetch all closed dates
function fetchClosedDates($conn) {
    $result = $conn->query("SELECT * FROM closed_dates");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to delete a guest by bookingID
function deleteGuest($conn, $bookingID) {
    $stmt = $conn->prepare("DELETE FROM clientbookings WHERE bookingID = ?");
    $stmt->bind_param("i", $bookingID); // Assuming bookingID is an integer
    return $stmt->execute();
}

// Handle date toggling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggleDate'])) {
    $dateToToggle = $_POST['date'];
    toggleClosedDate($conn, $dateToToggle);
}

// Handle deletion of a guest
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteGuest'])) {
    $bookingID = $_POST['bookingID']; // Use bookingID instead of guestId
    deleteGuest($conn, $bookingID);
    // Redirect to the same page to refresh the list
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Pagination settings
$limit = 10; // Number of entries to display per page
$date = date('Y-m-d'); // Current date
$offsetList = isset($_GET['guestListPage']) ? ($_GET['guestListPage'] - 1) * $limit : 0;
$offsetHistory = isset($_GET['guestHistoryPage']) ? ($_GET['guestHistoryPage'] - 1) * $limit : 0;

// Fetch current day bookings
$guestListResult = fetchGuests($conn, $username, $date, $limit, $offsetList);
$totalGuests = $guestListResult->num_rows;

// Fetch guest history (previous day)
$previousDate = date('Y-m-d', strtotime('-1 day'));
$guestHistoryResult = fetchGuestHistory($conn, $username, $previousDate, $limit, $offsetHistory);
$totalHistory = $guestHistoryResult->num_rows;

$closedDates = fetchClosedDates($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO</title>
    <link rel="stylesheet" href="../css/admin-book.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>
<style>
    .tab-content {
        margin: 10 auto;
        margin-left: 80px;
        max-width: 80%; /* Adjust width as needed */
    }
    .edit{
        margin-left: 600px;
        margin-top: 80px;
    }
</style>
<body>

<?php include '../includes/navigation-admin.php'; ?>
<div class="museum-background"></div>
<div class="admin-book-background">
    <div class="button-group text-center my-3">
        <button  onclick="showSection('guest-list'); event.preventDefault();">Guest List</button>
        <button  onclick="showSection('guest-history'); event.preventDefault();">Guest History</button>
        <button  onclick="showSection('edit-calendar'); event.preventDefault();">Edit Calendar</button>
    </div>

       <!-- Edit Calendar Section -->
<div class="tab-content edit" id="edit-calendar" style="display: none;">
        <form method="POST" action="" class="form-inline">
            <label for="date" class="mr-2">Select Date:</label>
            <input type="date" id="datePicker" name="date" class="form-control mr-2" value="<?php echo $date; ?>">
            <input type="submit" name="toggleDate" class="btn btn-secondary" value="Toggle Date">
        </form>
    <div id="status" class="mt-2 text-center"></div>
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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($totalGuests > 0): ?>
                <?php while ($row = $guestListResult->fetch_assoc()): ?>
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
                        <td><?php echo htmlspecialchars($row['appointmentTime'] ?? 'N/A'); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="bookingID" value="<?php echo htmlspecialchars($row['bookingID']); ?>">
                                <button type="submit" name="deleteGuest" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this guest?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center">No bookings found for today.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        <?php
        $totalPages = ceil($totalGuests / $limit);
        for ($i = 1; $i <= $totalPages; $i++) {
            echo "<a href='?guestListPage=$i' class='btn btn-outline-secondary mx-1'>$i</a>";
        }
        ?>
    </div>
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
                        <td><?php echo htmlspecialchars($row['appointmentTime'] ?? 'N/A'); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center">No previous bookings found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        <?php
        $totalHistoryPages = ceil($totalHistory / $limit);
        for ($i = 1; $i <= $totalHistoryPages; $i++) {
            echo "<a href='?guestHistoryPage=$i' class='btn btn-outline-secondary mx-1'>$i</a>";
        }
        ?>
    </div>
</div>


<script>
    function showSection(sectionId) {
        var sections = document.querySelectorAll('.tab-content');
        sections.forEach(function(section) {
            section.style.display = 'none';
        });
        document.getElementById(sectionId).style.display = 'block';
    }
</script>
</body>
</html>
