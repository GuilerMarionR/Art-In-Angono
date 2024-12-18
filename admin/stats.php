<?php
session_start(); // Start the session

// Include your database connection
include '../includes/db_connections.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the username from the session
$username = $_SESSION['username'];

// Fetch museum information based on the admin's username
$sql = "SELECT * FROM museums WHERE name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch museum info
    $museum = $result->fetch_assoc();
    $museumName = htmlspecialchars($museum['name']);
    $museumHistory = htmlspecialchars($museum['history']);
    $museumDescription = htmlspecialchars($museum['description']);
    $museumViews = htmlspecialchars($museum['views']); // Get the views count
} else {
    $museumName = "Museum not found";
    $museumHistory = "No history available.";
    $museumDescription = "No description available.";
    $museumViews = "N/A"; // Default value for views if no museum is found
}

// Get current date for comparison
$currentDate = date('Y-m-d');

// Initialize counters for past, present, and future bookings
$visits = [
    'past' => 0,
    'present' => 0,
    'future' => 0
];

// Fetch bookings for the museum
$sql = "SELECT appointmentDate FROM clientbookings WHERE museumName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $museumName);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $appointmentDate = $row['appointmentDate'];
    if ($appointmentDate < $currentDate) {
        $visits['past']++;
    } elseif ($appointmentDate == $currentDate) {
        $visits['present']++;
    } else {
        $visits['future']++;
    }
}

$stmt->close();

// Fetch views associated with this museum (just text, no graph)
$views = [];
$sql = "SELECT 360_URL FROM views WHERE museumName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $museumName);
$stmt->execute();
$result = $stmt->get_result();

while ($view = $result->fetch_assoc()) {
    $views[] = [
        'url' => htmlspecialchars($view['360_URL']),
    ];
}

$stmt->close();

// Fetch collections associated with this museum
$collections = [];
$sql = "SELECT image_url, description FROM collections WHERE museumName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $museumName);
$stmt->execute();
$result = $stmt->get_result();

while ($collection = $result->fetch_assoc()) {
    $collections[] = [
        'image' => htmlspecialchars($collection['image_url']),
        'description' => htmlspecialchars($collection['description'])
    ];
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Museum Stats</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
       /* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    text-align: center;
    padding: 20px;
}

/* Center the container */
.container {
    max-width: 800px;
    width: 90%;
    margin: 0 auto;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.museum-background {
    width: 100%;
    height: 200px;
    background: url('your-background-image.jpg') no-repeat center center;
    background-size: cover;
}

/* Button styles */
#downloadPDF {
    padding: 10px 20px;
    background-color: #C1121F;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
    margin-top: 20px;
}

#downloadPDF:hover {
    background-color: grey;
}

/* Chart container */
.chart-container {
    width: 100%;
    max-width: 600px;
    margin: 20px auto;
}
/* Button Styles */
.button {
    display: inline-block;
    padding: 10px 20px;
    margin: 10px;
    background-color: #6c757d; /* Default back button color */
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.button:hover {
    background-color: #5a6268; /* Darker grey on hover */
}


/* Responsive adjustments */
@media (max-width: 768px) {
    .container {
        width: 95%;
        padding: 15px;
    }
}

@media (max-width: 480px) {
    #downloadPDF {
        width: 100%;
    }
}

    </style>
</head>
<body>
<div class="museum-background"></div>
<?php include '../includes/navigation-admin.php'; ?>
<div class="container">
    <h1>Museum Stats for <?php echo $museumName; ?></h1>
    <h3>Total Visits: </h3>
    <p>Past Visits: <?php echo $visits['past']; ?></p>
    <p>Present Visits: <?php echo $visits['present']; ?></p>
    <p>Future Visits: <?php echo $visits['future']; ?></p>

    <div class="chart-container">
        <canvas id="museumChart"></canvas>
    </div>
    <h3>Museum's Total Views: <?php echo $museumViews; ?></h3>
    <button id="downloadPDF">Download PDF</button>
    <a href="admin-museums.php" class="button grey-button">Back</a>
   
</div>
    <script>
    // Data for the chart (Total visits in the past, present, and future)
    var visitsData = {
        past: <?php echo $visits['past']; ?>,
        present: <?php echo $visits['present']; ?>,
        future: <?php echo $visits['future']; ?>
    };

    // Create the bar chart
    var ctx = document.getElementById('museumChart').getContext('2d');
    var museumChart = new Chart(ctx, {
        type: 'bar',  // Bar chart
        data: {
            labels: ['Past Bookings', 'Present Bookings', 'Future Bookings'], // Categories
            datasets: [{
                label: 'Number of Bookings',
                data: [visitsData.past, visitsData.present, visitsData.future],
                backgroundColor: '#C1121F', // Bar color (with transparency)
                borderColor: '#C1121F', // Border color for the bars
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Booking Statistics: Past, Present, and Future'
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Booking Status'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Number of Bookings'
                    },
                    beginAtZero: true
                }
            }
        }
    });

    // Download the chart as a PDF
    document.getElementById('downloadPDF').addEventListener('click', function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Convert the chart to an image (base64)
        var chartImage = document.getElementById('museumChart').toDataURL("image/png");

        // Add the image to the PDF
        doc.addImage(chartImage, 'PNG', 10, 10, 180, 100);

        // Save the PDF
        doc.save('museum_stats.pdf');
    });
</script>

</body>
</html>
