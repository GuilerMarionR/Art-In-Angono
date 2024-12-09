<?php
session_start();

if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

include '../includes/db_connections.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

if (isset($_POST['bookingID'])) {
    $bookingID = $_POST['bookingID'];

    // Update booking status
    $updateStatusQuery = "UPDATE clientbookings SET status = 'Cancelled' WHERE bookingID = ?";
    $stmt = $conn->prepare($updateStatusQuery);
    $stmt->bind_param("i", $bookingID);
    $stmt->execute();
    $stmt->close();

    // Fetch email and appointment date for the user
    $emailQuery = "SELECT email, appointmentDate, museumName FROM clientbookings WHERE bookingID = ?";
    $stmt = $conn->prepare($emailQuery);
    $stmt->bind_param("i", $bookingID);
    $stmt->execute();
    $result = $stmt->get_result();
    $bookingDetails = $result->fetch_assoc();
    $stmt->close();

    $userEmail = $bookingDetails['email'];
    $appointmentDate = $bookingDetails['appointmentDate'];
    $museumName = $bookingDetails['museumName'];

    // Format the email message for the user
    $userEmailBody = "
        <p>Hello,</p>
        <p>We’ve received your request to cancel your appointment with us on <strong>$appointmentDate</strong> with an ID <strong>$bookingID</strong>.</p>
        <p>No worries—your booking has been canceled, and there’s no further action needed.</p>
        <p>If you’d like to reschedule, feel free to book a new appointment whenever it’s convenient.</p>
        <p>Thank you for using Art in Angono.</p>
    ";

    // Send email to the user
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
        $mail->addAddress($userEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Appointment Cancellation Confirmation';
        $mail->Body = $userEmailBody;
        $mail->AltBody = "We’ve received your request to cancel your appointment with us on $appointmentDate with an ID $bookingID. Your booking has been canceled.";

        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        echo json_encode(['status' => 'error', 'message' => 'Booking canceled, but email failed to send to the user.']);
        exit();
    }

    // Trigger admin email sending
    // Send a request to the separate script
    $adminNotificationData = [
        'bookingID' => $bookingID,
        'museumName' => $museumName,
        'appointmentDate' => $appointmentDate
    ];

    // Use cURL or a similar method to send the notification to the admin
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "send-cancel-admin.php");  // URL of the new PHP script
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $adminNotificationData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    echo json_encode(['status' => 'success', 'message' => 'Booking canceled and email sent to user. Admin notification triggered.']);
    exit();
}
?>
