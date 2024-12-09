<?php
session_start();
include '../includes/db_connections.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

if (!isset($_POST['bookingID']) || !isset($_POST['museumName']) || !isset($_POST['appointmentDate'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit();
}

$bookingID = $_POST['bookingID'];
$museumName = $_POST['museumName'];
$appointmentDate = $_POST['appointmentDate'];

// Fetch the admin's email based on the museumName
$adminQuery = "SELECT email FROM admins WHERE username = ?";
$stmt = $conn->prepare($adminQuery);
$stmt->bind_param("s", $museumName);
$stmt->execute();
$adminResult = $stmt->get_result();
$adminDetails = $adminResult->fetch_assoc();
$stmt->close();

$adminEmail = $adminDetails['email'];

// Format the email message for the admin
$adminEmailBody = "
    <p>Hello,</p>
    <p>The guest with the booking ID <strong>$bookingID</strong> has canceled their booking at your museum <strong>$museumName</strong>.</p>
    <p>The appointment scheduled on <strong>$appointmentDate</strong> has been canceled.</p>
    <p>Thank you for managing your museum's bookings on Art in Angono.</p>
";

// Send email to the admin
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
    $mail->addAddress($adminEmail);

    $mail->isHTML(true);
    $mail->Subject = 'Booking Cancellation Notification';
    $mail->Body = $adminEmailBody;
    $mail->AltBody = "The guest with booking ID $bookingID has canceled their booking for the appointment on $appointmentDate.";

    $mail->send();
} catch (Exception $e) {
    error_log("Mailer Error: {$mail->ErrorInfo}");
    echo json_encode(['status' => 'error', 'message' => 'Admin email failed to send.']);
    exit();
}

echo json_encode(['status' => 'success', 'message' => 'Admin notification sent successfully.']);
exit();
?>
