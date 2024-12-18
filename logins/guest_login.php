<?php
session_start();
include '../includes/db_connections.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - Guest Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Add Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>

.login-box {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;  /* Limit the width of the login box */
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 1.5rem;
}

/* Input group styling */
.input-group {
    margin-bottom: 15px;
}

.input-group label {
    display: block;
    font-size: 1rem;
    margin-bottom: 5px;
}

.input-group input {
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.password-container {
    position: relative;
}
.password-container .eye-icon {
    position: absolute;
    right: 10px;
    top: 60%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
}

/* Forgot password link and buttons */
.forgot-password {
    display: inline-block;
    margin-top: 10px;
    font-size: 0.9rem;
    color: #007bff;
}

.forgot-password:hover {
    text-decoration: underline;
}

button {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    font-size: 1rem;
    border: none;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    border-radius: 5px;
}

button:hover {
    background-color: #0056b3;
}

a {
    text-decoration: none;
}

.sign-up-link {
    display: block;
    text-align: center;
    margin-top: 10px;
    font-size: 1rem;
    color: #007bff;
}

.sign-up-link:hover {
    text-decoration: underline;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .login-box {
        padding: 15px;
        max-width: 90%;  /* Allow the login box to take up more space on smaller screens */
    }

    h2 {
        font-size: 1.2rem;
    }

    .input-group input {
        font-size: 0.9rem; /* Adjust input text size */
    }

    .password-container .eye-icon {
        font-size: 16px; /* Slightly smaller icon on mobile */
    }

    .forgot-password {
        font-size: 0.8rem;
    }

    button {
        font-size: 0.9rem; /* Adjust button font size */
    }
}

    </style>
</head>
<body>
<?php include '../includes/navigation-guest.php'; ?>
    <section class="loginbg">
        <div class="login-container">
            <div class="login-box">
                <h2>Guest Login</h2>
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
                    <a href="login.php" style="text-decoration:none;"><button>BACK</button></a>
                    <a href="sign_up.php" class="sign-up-link">Sign Up</a>
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
