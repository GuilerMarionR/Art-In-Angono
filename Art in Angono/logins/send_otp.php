<?php
session_start();
require '../includes/db_connections.php';
require '../vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists in the admins table
    $sql = "SELECT * FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
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

        // Now you can use $expires_at in your code
        echo $expires_at;

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
            $mail->Subject = 'Password Reset OTP';
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
        echo "Email not found.";
    }

    $stmt->close(); // Close the prepared statement
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
    <link rel="stylesheet" href="../css/custom-styles.css"> <!-- Link to custom CSS -->
    <style>
        /* Include the custom CSS styles defined above */
        /* Your previous styles here */
    </style>
</head>
<body>
    <?php include '../includes/navigation-guest.php'; ?> <!-- Include guest navigation -->
    <div class="museum-background"></div>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="text-center">Enter Your Email Address</h1>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block"  style="margin-left: 100px;">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

