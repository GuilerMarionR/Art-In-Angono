<?php
session_start();
include '../includes/db_connections.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $email = $_SESSION['email'];

    // Assign the password to a variable for binding without hashing
    $plain_password = $new_password;

    // Check if the email is in the admins table
    $sql_check_admin = "SELECT * FROM admins WHERE email = ?";
    $stmt_check_admin = $conn->prepare($sql_check_admin);
    $stmt_check_admin->bind_param("s", $email);
    $stmt_check_admin->execute();
    $result_admin = $stmt_check_admin->get_result();

    if ($result_admin->num_rows > 0) {
        // Email exists in admins table, update the password
        $sql_admin = "UPDATE admins SET password = ? WHERE email = ?";
        $stmt_admin = $conn->prepare($sql_admin);
        $stmt_admin->bind_param("ss", $plain_password, $email);
        $stmt_admin->execute();
    } else {
        // Check if the email is in the guests table
        $sql_check_guest = "SELECT * FROM guests WHERE email = ?";
        $stmt_check_guest = $conn->prepare($sql_check_guest);
        $stmt_check_guest->bind_param("s", $email);
        $stmt_check_guest->execute();
        $result_guest = $stmt_check_guest->get_result();

        if ($result_guest->num_rows > 0) {
            // Email exists in guests table, update the password
            $sql_guest = "UPDATE guests SET password = ? WHERE email = ?";
            $stmt_guest = $conn->prepare($sql_guest);
            $stmt_guest->bind_param("ss", $plain_password, $email);
            $stmt_guest->execute();
        } else {
            echo "<div class='alert alert-danger text-center'>Email not found in both admins and guests table.</div>";
            exit;
        }
    }

    // Remove OTP from otp_codes table to invalidate it
    $sql_otp = "DELETE FROM otp_codes WHERE email = ?";
    $stmt_otp = $conn->prepare($sql_otp);
    $stmt_otp->bind_param("s", $email);
    $stmt_otp->execute();

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
