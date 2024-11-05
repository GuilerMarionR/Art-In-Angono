<?php
session_start();
include '../includes/db_connections.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $email = $_SESSION['email'];

    // Assign the password to a variable for binding without hashing
    $plain_password = $new_password;

    // Update password in the admins table
    $sql = "UPDATE admins SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $plain_password, $email);
    $stmt->execute();

    // Remove OTP from otp_codes table to invalidate it
    $sql = "DELETE FROM otp_codes WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    echo "<div class='alert alert-success text-center'>Password has been reset successfully. You can now log in.</div>";
    unset($_SESSION['otp'], $_SESSION['email']); // Clear session data
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to right, #ffcccc, #ffffff); /* Soft gradient background */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            margin-top: 50px; /* Adjust margin for spacing */
            max-width: 400px; /* Set maximum width for the container */
        }

        h3 {
            color: #333;
            margin-bottom: 20px;
            font-weight: 700; /* Bold heading */
            text-align: center; /* Center the heading */
        }

        label {
            font-weight: 600; /* Make labels more prominent */
            color: #555;
        }

        .btn-danger {
            background-color: #dc3545; /* Bootstrap danger color */
            border: none;
            border-radius: 5px;
            padding: 12px; /* Consistent padding */
            font-size: 16px; /* Increase font size */
            width: 100%; /* Full width button */
        }

        .btn-danger:hover {
            background-color: #c82333; /* Darker red on hover */
        }
    </style>
</head>
<body>
    <?php include '../includes/navigation-guest.php'; // Include the navigation file?>
    <div class="museum-background"></div>
    <div class="container">
        <h3>Reset Password</h3>
        <form method="POST">
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-danger" style="margin-left:350px;">Reset Password</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
