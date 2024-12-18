<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - Public Notice</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom Table Styling */
.table {
    background-color: white;
    text-align: center;
}

.table th, .table td {
    vertical-align: middle;
    text-align: center;
}

.terms-container {
    padding: 20px;
}

/* Center content box */
.terms-box {
    background-color: #f9f9f9;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 900px; /* Limit the max width */
    margin: 0 auto;
}

/* Button container */
.button-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    gap: 10px;
    flex-wrap: wrap; /* Allow buttons to wrap on smaller screens */
}

/* Button styling */
.btn {
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    width: 48%; /* Make buttons take up 48% of the container's width */
}

/* Red button */
.red-btn {
    background-color: #ff4d4d;
    color: white;
}

/* Gray button */
.gray-btn {
    background-color: #e0e0e0;
    color: black;
}

/* On hover, add a hover effect */
.btn:hover {
    opacity: 0.8;
}

/* Adjust styles for mobile devices */
@media (max-width: 768px) {
    .terms-box {
        padding: 15px; /* Reduce padding on mobile */
    }

    .btn {
        font-size: 14px; /* Smaller font size on mobile */
        padding: 10px 18px; /* Adjust padding for mobile */
        width: 100%; /* Make buttons full width on mobile */
    }

    .button-container {
        gap: 20px;
    }
}

    </style>
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

    // Retrieve the museum parameter from the URL
    if (isset($_GET['museum']) && !empty($_GET['museum'])) {
        $museumName = htmlspecialchars($_GET['museum']); // Use htmlspecialchars to prevent XSS
    } else {
        $museumName = 'Unknown Museum'; // Default value if not set
    }

    include '../includes/db_connections.php';
    // Get the current date in 'Y-m-d' format
    $currentDate = date('Y-m-d');

    // Query to get closure dates and reasons for the museum
    $closedDatesQuery = "
        SELECT closed_date, reason 
        FROM closed_dates 
        WHERE museumName = '$museumName' AND closed_date >= '$currentDate'";
    $closedDatesResult = $conn->query($closedDatesQuery);
    $closedDates = [];

    if ($closedDatesResult) {
        while ($row = $closedDatesResult->fetch_assoc()) {
            $closedDates[] = $row;
        }
    }

    // Query to get closure times for the museum
    $closedTimesQuery = "
        SELECT date, startTime, endTime 
        FROM closed_times 
        WHERE museumName = '$museumName' AND date >= '$currentDate'";
    $closedTimesResult = $conn->query($closedTimesQuery);
    $closedTimes = [];

    if ($closedTimesResult) {
        while ($row = $closedTimesResult->fetch_assoc()) {
            $closedTimes[] = $row;
        }
    }

    // Close database connection
    $conn->close();
?>

<div class="terms-container">
    <div class="terms-box">
        <div class="terms-text">
            <h2>Notice: <?php echo $museumName; ?> Closure Information</h2>
            <div class="alert alert-warning" role="alert">
                <strong>Important Notice:</strong> The museum is closed on the following dates and times. Please make note of these dates when making your booking.
            </div>

            <div class="closure-dates">
                <h3>Closed Dates and Reasons</h3>
                <?php if (!empty($closedDates)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reason for Closing</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($closedDates as $date): ?>
                                <tr>
                                    <td><?php echo date('F j, Y', strtotime($date['closed_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($date['reason']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No closure dates found for this museum.</p>
                <?php endif; ?>
            </div>

            <div class="closure-times">
                <h3>Closed Times on Specific Dates</h3>
                <?php if (!empty($closedTimes)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($closedTimes as $time): ?>
                                <tr>
                                    <td><?php echo date('F j, Y', strtotime($time['date'])); ?></td>
                                    <td><?php echo date('g:i A', strtotime($time['startTime'])); ?></td>
                                    <td><?php echo date('g:i A', strtotime($time['endTime'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No closed times found for this museum.</p>
                <?php endif; ?>
            </div>

            <div class="button-container">
                <!-- Proceed to guest-form.php with museum name as a URL parameter -->
                <button class="btn red-btn" onclick="window.location.href='guest-form.php?museum=<?php echo urlencode($museumName); ?>'">AGREE</button>
                <button class="btn gray-btn" onclick="window.location.href='guest-book.php'">CANCEL</button>
            </div>
        </div>
    </div>
</div>

<script src="../js/scripts.js" defer></script>
</body>
</html>
