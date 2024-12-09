<?php
include '../includes/db_connections.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingID = $_POST['bookingID'];
    $newStatus = trim($_POST['updateStatus']); // Use trim to remove extra spaces

    // Ensure status is not empty
    if (empty($newStatus)) {
        die("Error: Status is required.");
    }

    // Retrieve guest details from the database
    $query = $conn->prepare("SELECT * FROM clientbookings WHERE bookingID = ?");
    $query->bind_param("i", $bookingID);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    
    if ($row) {
        // Get all the relevant fields from the row
        $firstName = $row['firstName'];
        $lastName = $row['lastName'];
        $middleName = $row['middleName'];
        $address = $row['address'];
        $email = $row['email'];
        $age = $row['age'];
        $contactNumber = $row['contactNumber'];
        $numberOfGuests = $row['numberOfGuests'];
        $appointmentDate = $row['appointmentDate'];
        $startTime = $row['startTime'];
        $endTime = $row['endTime'];
        $museumName = $row['museumName'];
        $status = $row['Status'];
    } else {
        die("Error: No guest found with this booking ID.");
    }

    // Validate the email address
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Invalid email address.");
    }

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'artinangono@gmail.com'; // Ideally, load this from a config file or environment
        $mail->Password = 'hlxy qreo jvpe rngy'; // This is sensitive, should ideally be from a secure source
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('artinangono@gmail.com', 'Art In Angono');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Booking Status Updated";

        // Set the email content based on the new status
        switch ($newStatus) {
            case 'Shown':
                $mail->Body = " <html>
                        <body>
                            <p>Dear $firstName $lastName,</p>
                            <p>Welcome to <strong>$museumName</strong>! We are glad to have you here today and hope you enjoy your stay here at our museum.</p>

                            <p>Warm regards,<br><strong>$museumName</strong></p>
                        </body>
                    </html>";
                break;
            case 'Not Shown':
                $mail->Body = " <html>
                        <body>
                            <p>Dear $firstName $lastName,</p>
                            <p>Here in <strong>$museumName</strong>, we just want to inform you that your booking with the booking ID of <strong>$bookingID</strong> has been tagged as <strong>Not Shown</strong>.</p>
                            <p>Due to the reason of not showing up at the appointed time of your booking.</p>

                            <p>Warm regards,<br><strong>$museumName</strong></p>
                        </body>
                    </html>";
                break;
            case 'Cancelled':
                $mail->Body = "<html>
                        <body>
                            <p>Dear $firstName $lastName,</p>
                            <p>Thank you for booking with <strong>$museumName</strong>. We just want to inform you that your booking with our museum has been cancelled due to your bad record on our museum bookings.</p>
                            <p>But don't worry! After 1 week of suspension, you will be allowed to book again with us.</p>
                            <p>Please take note that if you cancel or do not show up for your appointed time again, there will be further consequences to your future bookings.</p>

                            <p>Warm regards,<br><strong>$museumName</strong></p>
                        </body>
                    </html>";
                break;
            case 'Approved':
                $mail->Body = "<html>
                        <body>
                            <p>Hello, $firstName $lastName</p>
                            <p>Thank you for choosing <strong>$museumName</strong>! We would like to inform you that your booking with us has been <strong>$newStatus</strong>. We’re excited to welcome you. Here’s a quick summary of your booking details:</p>
                            
                            <p><strong>Name:</strong> $firstName $middleName $lastName</p>
                            <p><strong>Address:</strong> $address</p>
                            <p><strong>Email:</strong> $email</p>
                            <p><strong>Age:</strong> $age</p>
                            <p><strong>Contact Number:</strong> $contactNumber</p>
                            <p><strong>Number of Guests:</strong> $numberOfGuests</p>
                            <p><strong>Appointment Date:</strong> $appointmentDate</p>
                            <p><strong>Appointment Time:</strong> $startTime</p>

                            <p>Please note that this email will serve as your proof of transaction in the museum. Kindly present the appointment slip or this email in person upon arrival.</p>

                            <p>Warm regards,<br>Art In Angono<br><strong>$museumName</strong></p>
                        </body>
                    </html>";
                break;
            default:
                $mail->Body = "Dear $firstName $lastName, your booking status has been updated to $newStatus for your appointment on $appointmentDate at $startTime.";
                break;
        }

        // Send the email
        $mail->send();
    } catch (Exception $e) {
        // Log the error for debugging (without exposing sensitive data)
        error_log("Mailer Error: {$mail->ErrorInfo}");
        die("Error: The email could not be sent. Please try again later.");
    }
}
?>
