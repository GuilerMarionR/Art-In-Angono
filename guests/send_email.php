<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Check if form data is stored in session
if (isset($_SESSION['formData'])) {
    $formData = $_SESSION['formData'];

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

        // Recipients
        $mail->setFrom('artinangono@gmail.com', 'Art In Angono');
        $mail->addAddress($formData['email'], $formData['firstName'] . ' ' . $formData['lastName']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Appointment Confirmation';
        $mail->Body    = "<h1>Appointment Confirmation</h1>
                          <p>Good day! {$formData['firstName']} Thank you for choosing {$formData['museumName']}!</p>
                          <p>Here is your Booking Details</p>
                          <p><strong>Last Name:</strong> {$formData['lastName']}</p>
                          <p><strong>First Name:</strong> {$formData['firstName']}</p>
                          <p><strong>Middle Name:</strong> {$formData['middleName']}</p>
                          <p><strong>Address:</strong> {$formData['address']}</p>
                          <p><strong>Email:</strong> {$formData['email']}</p>
                          <p><strong>Age:</strong> {$formData['age']}</p>
                          <p><strong>Contact Number:</strong> {$formData['contactNumber']}</p>
                          <p><strong>Number of Guests:</strong> {$formData['numberOfGuests']}</p>
                          <p><strong>Appointment Date:</strong> {$formData['appointmentDate']}</p>
                          <p><strong>Appointment Time:</strong> {$formData['appointmentTime']}</p>";

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Form data is missing.";
}

?>
