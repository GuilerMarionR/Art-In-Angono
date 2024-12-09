<?php
session_start();
include '../includes/db_connections.php'; // Database connection

// Variables for error message and role
$error = "";
$role = isset($_POST['role']) ? $_POST['role'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($role === "admin") {
        // Admin login logic
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
    } elseif ($role === "guest") {
        // Guest login logic
        $sql_guest = "SELECT * FROM guests WHERE username = ? AND password = ?";
        $stmt_guest = $conn->prepare($sql_guest);

        if ($stmt_guest) {
            $stmt_guest->bind_param("ss", $username, $password);
            $stmt_guest->execute();
            $result_guest = $stmt_guest->get_result();

            if ($result_guest->num_rows > 0) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'guest';
                header("Location: ../index.php");
                exit();
            } else {
                $error = "Invalid guest username or password.";
            }
            $stmt_guest->close();
        }
    } else {
        $error = "Invalid role selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php include '../includes/navigation-guest.php'; ?>

    <!-- Background Image Section -->
    <section class="loginbg">
        <div class="login-container">
            <?php if ($role === null): ?>
                <!-- Role Selection -->
                <div class="login-box">
                    <h2>Are you a Guest or an Admin?</h2>
                    <form method="POST">
                        <button type="submit" name="role" value="guest" class="role-button">Guest</button>
                        <button type="submit" name="role" value="admin" class="role-button">Admin</button>
                    </form>
                </div>
            <?php else: ?>
                <!-- Login Form -->
                <div class="login-box">
                    <h2><?php echo ucfirst($role); ?> Login</h2>
                    <form method="POST">
                        <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">
                        <div class="input-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" placeholder="Enter your Username" required>
                        </div>
                        <div class="input-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter your Password" required>
                        </div>
                            <a href="send_otp.php" class="forgot-password">Forgot Password?</a>
                        <button type="submit" name="login">LOGIN</button>
                    </form>
                    <!-- Back Button -->
                    <form method="POST" style="margin-top: 20px; margin-left: 15px;">
                        <button type="submit" name="back" class="back-button">Back</button>
                    </form>
                    <?php if ($role === "guest"): ?>
                            <a href="sign_up.php" class="sign-up-link">Sign Up</a>
                        <?php endif; ?>
                    <div id="error-message" style="color: red; margin-top: 10px;">
                        <?php if ($error) echo htmlspecialchars($error); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>
