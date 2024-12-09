<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';


// Check if form data is stored in session
if (isset($_SESSION['formData'])) {
    $formData = $_SESSION['formData'];

    // Format appointment time to 12-hour format
    $formattedStartTime = date("h:i A", strtotime($formData['startTime']));
    $formattedEndTime = date("h:i A", strtotime($formData['endTime']));
    $appointmentTime = "$formattedStartTime - $formattedEndTime"; 

    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'artinangono@gmail.com';
        $mail->Password   = 'hlxy qreo jvpe rngy';  // Handle securely
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('artinangono@gmail.com', 'Art In Angono');
        $mail->addAddress($formData['email'], $formData['firstName'] . ' ' . $formData['lastName']);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Appointment Confirmation';
        $mail->Body = "
            <h1>Appointment Confirmation</h1>
            <p>Hello, </br>Thank you for choosing <strong>{$formData['museumName']}</strong>! We're excited to welcome you.</p>
            <p>Here's a quick summary of your booking details:</p>
            <p><strong>Name:</strong> {$formData['firstName']} {$formData['middleName']} {$formData['lastName']}</p>
            <p><strong>Address:</strong> {$formData['address']}</p>
            <p><strong>Email:</strong> {$formData['email']}</p>
            <p><strong>Age:</strong> {$formData['age']}</p>
            <p><strong>Contact Number:</strong> {$formData['contactNumber']}</p>
            <p><strong>Number of Guests:</strong> {$formData['numberOfGuests']}</p>
            <p><strong>Appointment Date:</strong> {$formData['appointmentDate']}</p>
            <p><strong>Appointment Time:</strong> $appointmentTime</p>
            <p>Please note that your reservation is still pending confirmation. Kindly present the appointment slip in person upon arrival.</p>
            <p>Warm Regards,</p>
            <p><strong>{$formData['museumName']}</strong></p>
        ";

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Form data is missing.";
}
?>
