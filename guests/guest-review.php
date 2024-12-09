<?php
// Include database connection
include '../includes/db_connections.php';

session_start(); // Start the session

// Initialize variables to store form data
$lastName = $_POST['lastName'] ?? '';
$firstName = $_POST['firstName'] ?? '';
$middleName = $_POST['middleName'] ?? '';
$address = $_POST['address'] ?? '';
$email = $_POST['email'] ?? '';
$age = $_POST['age'] ?? '';
$contactNumber = $_POST['contactNumber'] ?? '';
$numGuests = $_POST['numberOfGuests'] ?? '';
$appointmentDate = $_POST['appointmentDate'] ?? '';
$startTime = $_POST['startTime'] ?? '';
$endTime = $_POST['endTime'] ?? '';
$museumName = $_POST['museumName'] ?? '';
// Convert startTime and endTime to 24-hour format for database storage
$startTime24 = date("H:i", strtotime($startTime));
$endTime24 = date("H:i", strtotime($endTime));
// Store form data in session
$_SESSION['formData'] = [
    'lastName' => $lastName,
    'firstName' => $firstName,
    'middleName' => $middleName,
    'address' => $address,
    'email' => $email,
    'age' => $age,
    'contactNumber' => $contactNumber,
    'numberOfGuests' => $numGuests,
    'appointmentDate' => $appointmentDate,
    'startTime' => $startTime24,
    'endTime' => $endTime24,
    'museumName' => $museumName,
];

$message = '';
$bookingID = ''; // Initialize bookingID

// Proceed if form data is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (!empty($lastName) && !empty($firstName) && !empty($address) && 
        !empty($email) && !empty($contactNumber) && !empty($numGuests) && 
        !empty($appointmentDate) && !empty($startTime) && !empty($endTime) && !empty($museumName)) {

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Error: Invalid email format.";
        } elseif (!preg_match("/^\d{10,15}$/", $contactNumber)) {
            // Validate contact number format (10 to 15 digits)
            $message = "Error: Contact number must be between 10 to 15 digits.";
        } else {
            // Use username as userID
            $userID = $_SESSION['username'];
            
            $stmt = $conn->prepare("INSERT INTO clientbookings (username, lastName, firstName, middleName, address, email, age, contactNumber, numberOfGuests, appointmentDate, startTime, endTime, museumName, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $status = "Pending"; // Set status to 'pending'
            $stmt->bind_param(
                "ssssssssssssss",
                $userID, $lastName, $firstName, $middleName, $address, $email, $age, 
                $contactNumber, $numGuests, $appointmentDate, $startTime24, $endTime24, $museumName, $status
            );
            
            // Execute the statement
            if ($stmt->execute()) {
                // Get the auto-generated bookingID
                $bookingID = $conn->insert_id; // This should now correctly retrieve the auto-incremented bookingID

                // Provide a message to the user
                $message = "Booking successful! Your receipt has been generated.";

                // Trigger email sending (assumed you have a separate send_email.php)
                include '../guests/send_email.php'; // Ensure this includes the email sending logic

            } else {
                $message = "Error: Unable to save booking data.";
            }

            $stmt->close();
        }
    } else {
        $message = "Error: Missing required form data. Please fill in all fields.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - Review</title>
    <link rel="stylesheet" href="../css/guest-review.css">
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

    <div class="feedback-container">
    </div>
    <div class="background-container"></div>
    <div class="receipt-container">
        <div class="receipt-title">Appointment Receipt</div>
        <div class="receipt-details">
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($lastName); ?></p>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($firstName); ?></p>
            <p><strong>Middle Name:</strong> <?php echo htmlspecialchars($middleName); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($age); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($contactNumber); ?></p>
            <p><strong>Number of Guests:</strong> <?php echo htmlspecialchars($numGuests); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($appointmentDate); ?></p>
            <p><strong>Time:</strong> 
            <?php 
            echo htmlspecialchars(
                date("h:i A", strtotime($startTime24)) . ' - ' . date("h:i A", strtotime($endTime24))
            ); 
            ?>
        </p>
            <p><strong>Museum Name:</strong> <?php echo htmlspecialchars($museumName); ?></p>
            <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($bookingID); ?></p>
        </div>
        <div class="button-container">
        <button type="button" class="submit-button" onclick="handleDownload()">Download Receipt</button>
        </div>
    </div>

    <canvas id="receiptCanvas" style="display:none;"></canvas>

    <div class="footer">
    Thank you for booking with us! Please note that your reservation is still pending confirmation.
</br>Kindly present the Appointment Slip in person upon arrival.
    </div>

    <script>
        function handleDownload() {
    generateReceipt(); // Generate the receipt

    // Trigger the PHP script via AJAX
    fetch('../guests/send_email_admin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            bookingID: '<?php echo $bookingID; ?>',
            email: '<?php echo htmlspecialchars($email); ?>',
        })
    })
    .then(response => response.text())
    .then(data => {
        console.log('Email sent:', data); // Log success or show confirmation
    })
    .catch(error => {
        console.error('Error sending email:', error);
    });
}

        function generateReceipt() {
            // Get the canvas and context
            const canvas = document.getElementById('receiptCanvas');
            const ctx = canvas.getContext('2d');

            // Set the canvas dimensions (adjust as needed)
            canvas.width = 600;
            canvas.height = 600;

            // Fill the background
            ctx.fillStyle = '#ffffff'; // White background
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Set the text styles
            ctx.fillStyle = '#000000'; // Black text
            ctx.font = '20px Arial'; // Font size and family

            // Add text to the canvas using PHP variables directly
            ctx.fillText('Appointment Receipt', 50, 40);
            ctx.fillText('Last Name: <?php echo htmlspecialchars($lastName); ?>', 50, 80);
            ctx.fillText('First Name: <?php echo htmlspecialchars($firstName); ?>', 50, 120);
            ctx.fillText('Middle Name: <?php echo htmlspecialchars($middleName); ?>', 50, 160);
            ctx.fillText('Address: <?php echo htmlspecialchars($address); ?>', 50, 200);
            ctx.fillText('Email: <?php echo htmlspecialchars($email); ?>', 50, 240);
            ctx.fillText('Age: <?php echo htmlspecialchars($age); ?>', 50, 280);
            ctx.fillText('Contact Number: <?php echo htmlspecialchars($contactNumber); ?>', 50, 320);
            ctx.fillText('Number of Guests: <?php echo htmlspecialchars($numGuests); ?>', 50, 360);
            ctx.fillText('Date: <?php echo htmlspecialchars($appointmentDate); ?>', 50, 400);
            ctx.fillText(
                    'Time: <?php echo htmlspecialchars(date("h:i A", strtotime($startTime24)) . " - " . date("h:i A", strtotime($endTime24))); ?>', 
                    50, 440
                );
            ctx.fillText('Museum Name: <?php echo htmlspecialchars($museumName); ?>', 50, 480);
            ctx.fillText('Booking ID: <?php echo htmlspecialchars($bookingID); ?>', 50, 520);

            // Create a download link for the canvas
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = 'receipt.png';
            link.click();
        }
    </script>

</body>
</html>
