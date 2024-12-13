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