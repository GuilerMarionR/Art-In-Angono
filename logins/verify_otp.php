<?php
session_start();
include '../includes/db_connections.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = $_POST['otp'];
    $email = $_SESSION['email']; // Retrieve email from session

    // Verify OTP by checking if it matches and is not expired
    $sql = "SELECT * FROM otp_codes WHERE email = ? AND otp = ? AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $entered_otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // OTP is valid, redirect to reset password page
        header("Location: reset_password.php");
        exit;
    } else {
        echo "<div class='alert alert-danger text-center'>Invalid or expired OTP. Please try again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            <h1 class="text-center mb-4">Verify OTP</h1>
            <form method="POST">
                <div class="form-group">
                    <label for="otp">Enter OTP:</label>
                    <input type="text" id="otp" name="otp" class="form-control" required>
                </div>
                <div class="d-flex justify-content-between mt-4">
                <a href="login.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-danger">Verify</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
