<?php
session_start();
include '../includes/db_connections.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql_admin = "SELECT * FROM admins WHERE user_name = ? AND password = ?";
    $stmt_admin = $conn->prepare($sql_admin);

    if ($stmt_admin) {
        $stmt_admin->bind_param("ss", $username, $password);
        $stmt_admin->execute();
        $result_admin = $stmt_admin->get_result();

        if ($result_admin->num_rows > 0) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'admin';
            header("Location: ../admin/admin-home.php");
            exit();
        } else {
            $error = "Invalid admin username or password.";
        }
        $stmt_admin->close();
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
    <!-- Add Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .password-container {
            position: relative;
            width: 100%;
        }
        .password-container input {
            width: 100%;
            padding-right: 40px; /* Space for the eye icon */
        }
        .password-container .eye-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
    margin-top: 10px; /* Add left margin */
}

    </style>
</head>
<body>
<?php include '../includes/navigation-guest.php'; ?>
    <section class="loginbg">
        <div class="login-container">
            <div class="login-box">
                <h2>Admin Login</h2>
                <form method="POST">
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="input-group password-container">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        <span class="eye-icon" id="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye"></i> <!-- Font Awesome Eye Icon -->
                        </span>
                    </div>
                    <a href="send_otp.php" class="forgot-password">Forgot Password?</a>
                    <button type="submit">LOGIN</button>
                    <button onclick="window.location.href='login.php'" style="text-decoration:none;">BACK</button>
                    <?php if (!empty($error)): ?>
                    <div id="error-message" style="color: red;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </section>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var toggleIcon = document.getElementById("toggle-password");
            var icon = toggleIcon.querySelector('i');
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash"); // Change icon to eye-slash
            } else {
                passwordField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye"); // Change icon back to eye
            }
        }
    </script>
</body>
</html>
