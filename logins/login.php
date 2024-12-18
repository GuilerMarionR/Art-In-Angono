<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - Select Role</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<style>
    /* Default container width */

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

/* Button styles */
.role-button {
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

.role-button:hover {
    background-color: #0056b3;
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
}

</style>
<body>
    <?php include '../includes/navigation-guest.php'; ?>
    <section class="loginbg">
        <div class="login-container">
            <div class="login-box">
                <h2>Are you a Guest or an Admin?</h2>
                <form method="POST" action="admin_login.php">
                    <button type="submit" name="role" value="admin" class="role-button">Admin</button>
                </form>
                <form method="POST" action="guest_login.php">
                    <button type="submit" name="role" value="guest" class="role-button">Guest</button>
                </form>
            </div>
        </div>
    </section>
</body>
</html>