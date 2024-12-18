<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    
</head>
<style>
    
        /* Hamburger Menu Styles */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        .hamburger div {
            width: 25px;
            height: 3px;
            background-color: white;
        }

        /* Menu Links */
        .menu {
            display: flex;
            gap: 15px;
        }

        .menu a {
            color: white;
            text-decoration: none;
            font-size: 1rem;
            padding: 10px 15px;
            transition: background-color 0.3s ease;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .hamburger {
                display: flex; /* Shows hamburger on mobile */
            }

            .menu {
                display: none;
                flex-direction: column;
                background-color: #C1121F;
                position: absolute;
                top: 60px;
                right: 0;
                width: 100%;
                padding: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .menu.active {
                display: flex;
            }

            .menu a {
                text-align: center;
                width: 100%;
            }
            .logo img{
    width:40px;
    height: 50px;
}
.logo span{
    font-size: 20px;
}
        }
</style>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <!-- Logo Section -->
        <div class="logo">
            <img src="https://i.imgur.com/BeaSiLw.png" alt="Logo" class="logo-img">
            <span>ART IN ANGONO</span>
        </div>

        <!-- Hamburger Icon -->
        <div class="hamburger" onclick="toggleMenu()">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <!-- Menu Section -->
        <div class="menu">
            <a href="admin-home.php">HOME</a>
            <a href="admin-museums.php">MUSEUMS</a>
            <a href="admin-news.php">NEWS & EVENTS</a>
            <a href="admin-art.php">ARTWORKS</a>
            <a href="admin-book.php">BOOK A TOUR</a>
            <a href="../logins/logout.php">LOGOUT</a>
        </div>
    </div>
    <script>
        // JavaScript to toggle the mobile menu visibility
        function toggleMenu() {
            const menu = document.querySelector('.menu');
            menu.classList.toggle('active');
        }
    </script>

</body>
</html>