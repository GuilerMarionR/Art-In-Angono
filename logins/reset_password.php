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
    <link rel="stylesheet" href="../css/style.css">
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
    <div class=" mt-5 d-flex justify-content-center">
        <div class="custom-container">
            <h1 class="text-center mb-4">Reset Password</h1>
            <form method="POST" >
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                </div>
                <div class="d-flex justify-content-between mt-4">
                <a href="login.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-danger">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
