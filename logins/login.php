<?php  
session_start();
include '../includes/db_connections.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username']; // Get username from form
    $password = $_POST['password']; // Get password from form

    // Prepare SQL statement to prevent SQL injection
    $sql = "SELECT * FROM admins WHERE user_name = ? AND password = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Login successful
            $_SESSION['username'] = $username;
            header("Location: ../admin/admin-home.php"); // Redirect to admin dashboard
            exit(); // Important to call exit after header redirection
        } else {
            $error = "Invalid username or password."; // Error message for invalid login
        }
        $stmt->close(); // Close the prepared statement
    } else {
        $error = "Database error: " . $conn->error; // Handle error preparing statement
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include '../includes/navigation-guest.php'; ?>

    <!-- Background Image Section -->
    <section class="loginbg">
        <div class="login-container">
            <div class="login-box">
                <h2><i class="lock-icon">🔒</i> ADMIN LOGIN</h2>
                <form id="loginForm" method="POST"> <!-- Removed action attribute; it submits to the same page -->
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter your Username" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your Password" required>
                    </div>

                    <!-- Forgot Password Modal Trigger -->
                    <a href="send_otp.php" class="forgot-password" onclick="openForgotPasswordModal()">Forgot Password?</a>

                    <button type="submit">LOGIN</button>
                </form>
                <div id="error-message" style="color: rgb(255, 0, 0); margin-top: 10px;"><?php if (isset($error)) echo $error; ?></div> <!-- Display error message -->
            </div>
        </div>
    </section>
</body>
</html>
