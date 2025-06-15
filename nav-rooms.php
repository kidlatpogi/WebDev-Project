<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$full_first_name = $_SESSION['first_name'] ?? 'User';
$user_first_name = explode(' ', trim($full_first_name))[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>

    <link rel="stylesheet" href="nav-rooms.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
    <!-- Desktop Navbar -->
    <nav class="desktop-navbar">
    
    <!--NU Logo -->
    <div class="logo-container">
        <a href="nav-home.php"><img src="Photos/nu-logo-label-blue.png" class="nu-logo"></a>
    </div>

    <!-- Clicable Navigation -->
    <ul>
        <li><a href="nav-home.php">Home</a></li>
        <li><a href="nav-rooms.php">Rooms</a></li>
        <li><a href="nav-reservation.php">Reservation</a></li>
        <li><a href="nav-account.php">Account</a></li>
        <li><a href="nav-history.php">History</a></li>
    </ul>

    <!-- User drowdown -->
    <!-- Logout -->
    <div class="user-dropdown-wrapper">
        <div id="user-dropdown">
            Hi, <?php echo htmlspecialchars($user_first_name); ?>
            <i class="fas fa-chevron-down dropdown-icon"></i>
            <div id="logout-menu">
                <button onclick="logoutUser()"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>

        </div>
    </div>
    </nav>

    <!-- Hamburger Navigation Start -->
    <div class="hamburger-menu">

    <input id="menu__toggle" type="checkbox" />

    <label class="menu__btn" for="menu__toggle">
        <span></span>
    </label>

        <ul class="menu__box">
            <li><a class="menu__item" href="nav-home.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a class="menu__item active" href="nav-rooms.php"><i class="fas fa-door-open"></i> Rooms</a></li>
            <li><a class="menu__item" href="nav-reservation.php"><i class="fas fa-calendar-check"></i> Reservation</a></li>
            <li><a class="menu__item" href="nav-account.php"><i class="fas fa-user-circle"></i> Account</a></li>
            <li><a class="menu__item" href="nav-history.php"><i class="fas fa-history"></i> History</a></li>
            <li><a class="menu__item" href="index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <!-- Hamburger Navigation End -->

    <!-- Header -->
    <div class="header-center">
            <h1>Available Rooms</h1>
            <p class="subtitle">Select your preferred study space</p>
        </div>

    <!-- Filter Controls -->
        <div class="filter-controls">
            <div class="filter-group">
                <h3><i class="fas fa-building"></i> Floor Selection</h3>
                <div class="radio-toolbar">
                    <input type="radio" id="4th-floor" name="floor" value="4th Floor" checked>
                    <label for="4th-floor">4th Floor</label>
                    <input type="radio" id="5th-floor" name="floor" value="5th Floor">
                    <label for="5th-floor">5th Floor</label>
                </div>
            </div>

            <div class="filter-group">
                <h3><i class="fas fa-door-closed"></i> Room Type</h3>
                <div class="radio-toolbar">
                    <input type="radio" id="lecture-room" name="room-type" value="Lecture Room" checked>
                    <label for="lecture-room">Lecture Room</label>
                    <input type="radio" id="laboratory-room" name="room-type" value="Laboratory Room">
                    <label for="laboratory-room">Laboratory</label>
                </div>
            </div>
        </div>

    <!-- Room grid -->
    <div id="room-grid"></div>

    <!-- Photo Modal: Update to include a description box beside the image -->
    <div id="photo-modal">
        <div id="modal-content">
            <img id="modal-img" src="" alt="Room Preview">
            <div id="modal-desc"></div>
        </div>
    </div>

    <script src="nav-rooms.js"></script>
    <script src="function-logout.js"></script>

    <!-- Active Navbar -->
    <script>
        document.querySelectorAll('.desktop-navbar li a').forEach(link => {
        const linkPath = new URL(link.href).pathname;
        const currentPath = window.location.pathname;

        if (linkPath === currentPath || currentPath.endsWith(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });
    </script>
</body>
</html>