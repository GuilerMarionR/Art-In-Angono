<?php
session_start();
include '../includes/db_connections.php';

$firstName = $lastName = $middleName = $birthdate = $address = $email = $contactNumber = $username = $password = $confirmPassword = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $middleName = $_POST['middleName'];
    $birthdate = $_POST['birthdate'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $contactNumber = $_POST['contactNumber'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Check if the email is already in use
    $emailCheckQuery = "SELECT * FROM guests WHERE email = ?";
    $stmt = $conn->prepare($emailCheckQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "The email address is already in use. Please use a different email.";
    } else {
        // Check if the username contains at least one symbol and one number
        if (!preg_match('/[0-9]/', $username) || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $username)) {
            $error = "Username must contain at least one number and one symbol.";
        } else {
            // Proceed with registration if all conditions are met
            if ($password === $confirmPassword) {
                // Check password requirements
                if (strlen($password) < 10 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
                    $error = "Password must be at least 10 characters long, contain a capital letter, and a number.";
                } else {
                    // Prepare and execute insertion if passwords match
                    $sql = "INSERT INTO guests (firstName, lastName, middleName, birthdate, address, email, contactNumber, username, password)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);

                    if ($stmt) {
                        $stmt->bind_param("sssssssss", $firstName, $lastName, $middleName, $birthdate, $address, $email, $contactNumber, $username, $password);
                        if ($stmt->execute()) {
                            $_SESSION['message'] = "Registration successful! Please log in.";
                            header("Location: guest_login.php");
                            exit();
                        } else {
                            $error = "Error: Could not register. Please try again.";
                        }
                    } else {
                        $error = "Database error: " . $conn->error;
                    }
                }
            } else {
                $error = "Passwords do not match. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Signup</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
       body {
    background: linear-gradient(to right, #ffcccc, #ffffff);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.container {
    margin-top: 30px;
    margin-bottom: 20px;
    max-width: 500px;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h3 {
    color: #333;
    margin-bottom: 20px;
    font-weight: 700;
    text-align: center;
}

label {
    font-weight: 600;
    color: #555;
}

.form-control {
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    font-size: 16px;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

.btn-primary {
    background-color: #007bff;
    border: none;
    border-radius: 5px;
    padding: 12px;
    font-size: 16px;
    width: 100%;
    transition: background-color 0.3s;
}

.btn-primary:hover {
    background-color: #0056b3;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .container {
        margin-top: 10px;
        margin-bottom: 10px;
        padding: 15px;
        max-width: 90%; /* Allow the form to take more space on smaller screens */
    }

    h3 {
        font-size: 1.4rem;
        margin-bottom: 15px;
    }

    .form-control {
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 10px;
    }

    .form-group label {
        font-size: 14px;
    }

    .form-group input,
    .form-group textarea {
        font-size: 14px;
    }

    .btn-danger {
        padding: 10px;
        font-size: 14px;
        margin-left:120px;
    }

    .col-md-6 {
        padding-left: 0;
        padding-right: 0;
    }
}

@media (max-width: 480px) {
    .container {
        max-width: 100%;
    }

    .form-group {
        margin-bottom: 8px;
    }

    h3 {
        font-size: 1.2rem;
    }

    .form-control {
        font-size: 12px;
    }

    .btn-danger {
        padding: 8px;
        font-size: 12px;
        margin-left:120px;
    }

    .col-md-6 {
        width: 100%;
        margin-bottom: 10px; /* Stack form fields vertically on very small screens */
    }

    .login-link p {
        font-size: 12px;
    }
}

    </style>
</head>
<body>
    <?php include '../includes/navigation-guest.php'; ?>
    <?php if (isset($error)) echo "<div class='alert alert-danger text-center mt-3'>$error</div>"; ?>
    <div class="container">
        <h3>Guest Signup</h3>
        <form method="POST" id="signupForm">
            <div class="form-group row">
                <div class="col-md-6">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" class="form-control" value="<?= htmlspecialchars($firstName); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" class="form-control" value="<?= htmlspecialchars($lastName); ?>" required>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label for="middleName">Middle Name</label>
                    <input type="text" id="middleName" name="middleName" class="form-control" value="<?= htmlspecialchars($middleName); ?>">
                </div>
                <div class="col-md-6">
                    <label for="birthdate">Birthdate</label>
                    <input type="date" id="birthdate" name="birthdate" class="form-control" value="<?= htmlspecialchars($birthdate); ?>" required>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="contactNumber">Contact Number</label>
                    <input type="text" id="contactNumber" name="contactNumber" class="form-control" value="<?= htmlspecialchars($contactNumber); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" class="form-control" required><?= htmlspecialchars($address); ?></textarea>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($username); ?>" required>
                <small class="form-text text-muted">Username must contain at least one number and one symbol (e.g., @, #, $, etc.).</small>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <small id="passwordHelp" class="form-text text-muted">Password must be at least 10 characters long, contain a capital letter, and a number.</small>
                </div>
                <div class="col-md-6">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                    <small id="matchHelp" class="form-text text-muted"></small>
                </div>
            </div>

            <button type="submit" class="btn btn-danger" style="margin-left:135px;">Sign Up</button>
        </form>
        <div class="login-link mt-3 text-center">
            <p>Already have an account? <a href="guest_login.php">Log in here</a>.</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('password').addEventListener('input', function() {
            var password = this.value;
            var confirmPassword = document.getElementById('confirmPassword').value;
            var passwordHelp = document.getElementById('passwordHelp');
            var matchHelp = document.getElementById('matchHelp');

            // Check password requirements
            if (password.length < 10 || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
                passwordHelp.style.color = "red";
            } else {
                passwordHelp.style.color = "green";
            }

            // Check if passwords match
            if (password !== confirmPassword) {
                matchHelp.style.color = "red";
                matchHelp.textContent = "Passwords do not match.";
            } else {
                matchHelp.style.color = "green";
                matchHelp.textContent = "Passwords match.";
            }
        });

        document.getElementById('confirmPassword').addEventListener('input', function() {
            var password = document.getElementById('password').value;
            var confirmPassword = this.value;
            var matchHelp = document.getElementById('matchHelp');

            // Check if passwords match
            if (password !== confirmPassword) {
                matchHelp.style.color = "red";
                matchHelp.textContent = "Passwords do not match.";
            } else {
                matchHelp.style.color = "green";
                matchHelp.textContent = "Passwords match.";
            }
        });

        document.getElementById('contactNumber').addEventListener('input', function() {
            const contactNumber = this.value;
            if (!/^\d{11}$/.test(contactNumber)) {
                this.setCustomValidity("Contact number must be exactly 11 digits.");
            } else {
                this.setCustomValidity("");
            }
        });

        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                this.setCustomValidity("Please enter a valid email address.");
            } else {
                this.setCustomValidity("");
            }
        });
    </script>
</body>
</html>
