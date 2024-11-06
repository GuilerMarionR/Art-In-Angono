<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include '../includes/db_connections.php';

session_start();

// Check if POST data is set
if (isset($_POST['bookingID']) && isset($_POST['museumName'])) {
    // Retrieve booking details from session
    $formData = $_SESSION['formData'] ?? [];
    
    $bookingID = $_POST['bookingID'];
    $museumName = $_POST['museumName'];

    // Prepare and execute the query to get admin details
    $stmt = $conn->prepare("SELECT email, user_name FROM admins WHERE user_name = ?");
    $stmt->bind_param("s", $museumName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'artinangono@gmail.com';
            $mail->Password   = 'hlxy qreo jvpe rngy'; // Handle securely
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Set the sender
            $mail->setFrom('artinangono@gmail.com', 'Art In Angono');

            // Send email to the admin
            $mail->addAddress($admin['email'], $admin['user_name']);

            // Content for the admin
            $mail->isHTML(true);
            $mail->Subject = 'New Booking Notification';
            $mail->Body    = "<h1>New Booking Received</h1>
                              <p>You have a new booking with ID: <strong>{$bookingID}</strong>.</p>
                              <p><strong>Booking Details:</strong></p>
                              <ul>
                                <li><strong>Last Name:</strong> {$formData['lastName']}</li>
                                <li><strong>First Name:</strong> {$formData['firstName']}</li>
                                <li><strong>Middle Name:</strong> {$formData['middleName']}</li>
                                <li><strong>Address:</strong> {$formData['address']}</li>
                                <li><strong>Email:</strong> {$formData['email']}</li>
                                <li><strong>Age:</strong> {$formData['age']}</li>
                                <li><strong>Contact Number:</strong> {$formData['contactNumber']}</li>
                                <li><strong>Number of Guests:</strong> {$formData['numberOfGuests']}</li>
                                <li><strong>Appointment Date:</strong> {$formData['appointmentDate']}</li>
                                <li><strong>Appointment Time:</strong> {$formData['appointmentTime']}</li>
                                <li><strong>Museum Name:</strong> {$formData['museumName']}</li>
                              </ul>
                              <p>Please check the details in your admin panel.</p>";

            $mail->send(); // Send email to the admin
            echo "Email sent to the admin.";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "No matching admin found for the provided museum name.";
    }

    $stmt->close();
} else {
    echo "Required data is missing.";
}

$conn->close();
?>
