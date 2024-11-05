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
$appointmentTime = $_POST['appointmentTime'] ?? '';
$museumName = $_POST['museumName'] ?? '';

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
    'appointmentTime' => $appointmentTime,
    'museumName' => $museumName,
];

$message = '';
$bookingID = ''; // Initialize bookingID

// Proceed if form data is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (!empty($lastName) && !empty($firstName) && !empty($address) && 
        !empty($email) && !empty($contactNumber) && !empty($numGuests) && 
        !empty($appointmentDate) && !empty($appointmentTime) && !empty($museumName)) {

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Error: Invalid email format.";
        } elseif (!preg_match("/^\d{10,15}$/", $contactNumber)) {
            // Validate contact number format (10 to 15 digits)
            $message = "Error: Contact number must be between 10 to 15 digits.";
        } else {
            // Generate userID (you can change this logic as needed)
            $userID = uniqid('user_', true); // Example: Generate a unique userID
            
            // Insert into the database
            $stmt = $conn->prepare("INSERT INTO clientbookings (userID, lastName, firstName, middleName, address, email, age, contactNumber, numberOfGuests, appointmentDate, appointmentTime, museumName) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssssss", $userID, $lastName, $firstName, $middleName, $address, $email, $age, $contactNumber, $numGuests, $appointmentDate, $appointmentTime, $museumName);

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
    <title>ART IN ANGONO</title>
    <link rel="stylesheet" href="../css/guest-review.css">
</head>
<body>

    <?php include '../includes/navigation-guest.php'; ?>
    <div class="feedback-container">
        <p><?php echo htmlspecialchars($message); ?></p>
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
            <p><strong>Time:</strong> <?php echo htmlspecialchars($appointmentTime); ?></p>
            <p><strong>Museum Name:</strong> <?php echo htmlspecialchars($museumName); ?></p>
            <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($bookingID); ?></p>
        </div>
        <div class="button-container">
            <button type="button" class="submit-button" onclick="generateReceipt()">Download Receipt</button>
        </div>
    </div>

    <canvas id="receiptCanvas" style="display:none;"></canvas>

    <div class="footer">
        Thank you for booking with us! We look forward to seeing you soon.
    </div>

    <script>
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
    ctx.fillText('Time: <?php echo htmlspecialchars($appointmentTime); ?>', 50, 440);
    ctx.fillText('Museum Name: <?php echo htmlspecialchars($museumName); ?>', 50, 480);
    ctx.fillText('Booking ID: <?php echo htmlspecialchars($bookingID); ?>', 50, 520);

    // Trigger download
    const link = document.createElement('a');
    link.download = 'receipt.png';
    link.href = canvas.toDataURL();
    link.click();

    // Send AJAX request to trigger email to admin
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "send_email_admin.php", true); // URL to your email sending script
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status === 200) {
            console.log("Email sent to admin.");
        } else {
            console.error("Error sending email to admin.");
        }
    };
    xhr.send("bookingID=<?php echo $bookingID; ?>&museumName=<?php echo urlencode($museumName); ?>");
}

    </script>
</body>
</html>
