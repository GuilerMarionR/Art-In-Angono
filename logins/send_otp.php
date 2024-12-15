<?php
session_start();
require '../includes/db_connections.php';
require '../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists in the guests table
    $sql_guests = "SELECT * FROM guests WHERE email = ?";
    $stmt_guests = $conn->prepare($sql_guests);
    $stmt_guests->bind_param("s", $email);
    $stmt_guests->execute();
    $result_guests = $stmt_guests->get_result();

    // Check if email exists in the admins table
    $sql_admins = "SELECT * FROM admins WHERE email = ?";
    $stmt_admins = $conn->prepare($sql_admins);
    $stmt_admins->bind_param("s", $email);
    $stmt_admins->execute();
    $result_admins = $stmt_admins->get_result();

    if ($result_guests->num_rows > 0 || $result_admins->num_rows > 0) {
        // Generate OTP
        $otp = rand(100000, 999999);

        // Set the timezone to PHT (Philippine Time)
        date_default_timezone_set('Asia/Manila');

        // Create a DateTime object for the current time
        $currentTime = new DateTime();

        // Add 5 minutes to the current time
        $currentTime->modify('+5 minutes');

        // Format the time as needed (e.g., 'Y-m-d H:i:s' for MySQL datetime)
        $expires_at = $currentTime->format('Y-m-d H:i:s');

        // Insert OTP and expiration time into otp_codes table
        $sql = "INSERT INTO otp_codes (email, otp, expires_at) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $email, $otp, $expires_at);
        if (!$stmt->execute()) {
            echo "Error saving OTP: " . $stmt->error;
            exit;
        }

        // Send OTP via email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Use Gmail SMTP server
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'artinangono@gmail.com'; // Replace with your actual Gmail address
            $mail->Password = 'hlxy qreo jvpe rngy'; // Replace with your actual Gmail password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
            $mail->Port = 587; // TCP port to connect to

            // Debugging output
            $mail->SMTPDebug = 2; // Set to 0 to disable debug output, 2 for detailed output

            // Recipients
            $mail->setFrom('artinangono@gmail.com', 'Art in Angono'); // Replace with your sender email
            $mail->addAddress($email); // Recipient email
            $mail->Subject = 'OTP for Email Verification';
            $mail->isHTML(true); // Set email format to HTML
            $mail->Body = "Your OTP code is <strong>$otp</strong>";

            // Send the email
            $mail->send();
            $_SESSION['email'] = $email; // Store email in session for later verification
            header("Location: verify_otp.php"); // Redirect to OTP verification page
            exit;
        } catch (Exception $e) {
            echo "Error sending email: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email not found in guests or admins table.";
    }

    // Close the prepared statements
    $stmt_guests->close();
    $stmt_admins->close();
    $conn->close(); // Close the database connection
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/custom-styles.css">
    <style>
        .custom-container {
            width: 400px;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <?php include '../includes/navigation-guest.php'; ?>
    <div class="museum-background"></div>
    <div class="mt-5 d-flex justify-content-center">
        <div class="custom-container">
            <h1 class="text-center mb-4">Enter Your Email</h1>
            <form action="" method="post">
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="d-flex justify-content-between mt-4">
                <a href="login.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
