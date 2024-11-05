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
        body {
            background: linear-gradient(to right, #ffcccc, #ffffff); /* Soft gradient background */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            margin-top: 50px; /* Adjust margin for spacing */
            max-width: 400px; /* Set maximum width for the container */
            padding: 20px; /* Add padding for aesthetics */
            background-color: #fff; /* White background for the form */
            border-radius: 8px; /* Rounded corners for the container */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow effect */
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

        input[type="text"] {
            border: 1px solid #ccc; /* Light border */
            border-radius: 5px;
            padding: 10px; /* Padding for comfort */
            font-size: 16px; /* Increase font size */
            transition: border-color 0.3s, box-shadow 0.3s; /* Smooth transition */
        }

        input[type="text"]:focus {
            border-color: #007bff; /* Focus border color */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Shadow on focus */
        }

        .btn-danger {
            background-color: #dc3545; /* Bootstrap danger color */
            border: none;
            border-radius: 5px;
            padding: 12px; /* Consistent padding */
            font-size: 16px; /* Increase font size */
            width: 100%; /* Full width button */
            transition: background-color 0.3s; /* Smooth background color transition */
        }

        .btn-danger:hover {
            background-color: #c82333; /* Darker red on hover */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Add shadow on hover */
        }
    </style>
</head>
<body>
    <?php include '../includes/navigation-guest.php';?>
    <div class="museum-background"></div>
    <div class="container">
        <h3>Verify OTP</h3>
        <form method="POST">
            <div class="form-group">
                <label for="otp">Enter OTP:</label>
                <input type="text" id="otp" name="otp" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-danger" style="margin-left: 350px;">Verify</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
