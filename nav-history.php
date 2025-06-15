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
    <title>History | NU Room Reservation</title>
    <link rel="stylesheet" href="nav-history.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Desktop Navbar -->
    <nav class="desktop-navbar">
        <!-- NU Logo -->
        <div class="logo-container">
            <a href="nav-home.php"><img src="Photos/nu-logo-label-blue.png" class="nu-logo" alt="NU Logo"></a>
        </div>
        
        <!-- Clickable Navigation -->
        <ul>
            <li><a href="nav-home.php">Home</a></li>
            <li><a href="nav-rooms.php">Rooms</a></li>
            <li><a href="nav-reservation.php">Reservation</a></li>
            <li><a href="nav-account.php">Account</a></li>
            <li><a href="nav-history.php" class="active">History</a></li>
        </ul>

        <!-- User dropdown -->
        <div class="user-dropdown-wrapper">
            <div id="user-dropdown">
                <span class="user-greeting">Hi, <?php echo htmlspecialchars($user_first_name); ?></span>
                <i class="fas fa-chevron-down dropdown-icon"></i>

                <div id="logout-menu">
                    <button onclick="logoutUser()"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hamburger Navigation -->
    <div class="hamburger-menu">
        <input id="menu__toggle" type="checkbox" />
        <label class="menu__btn" for="menu__toggle">
            <span></span>
        </label>

        <ul class="menu__box">
            <li><a class="menu__item" href="nav-home.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a class="menu__item" href="nav-rooms.php"><i class="fas fa-door-open"></i> Rooms</a></li>
            <li><a class="menu__item" href="nav-reservation.php"><i class="fas fa-calendar-check"></i> Reservation</a></li>
            <li><a class="menu__item" href="nav-account.php"><i class="fas fa-user-circle"></i> Account</a></li>
            <li><a class="menu__item active" href="nav-history.php"><i class="fas fa-history"></i> History</a></li>
            <li><a class="menu__item" href="index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <main class="history-container">
        <!-- Page Header -->
        <header class="history-header">
            <h1><i class="fas fa-history"></i> Reservation History</h1>
            <p class="subtitle">View your past and current room reservations</p>
        </header>

        <!-- Status Filter -->
        <div class="filter-container">
            <div class="radio-toolbar">
                <input type="radio" id="ongoing" name="history-status" value="Ongoing" checked>
                <label for="ongoing"><i class="fas fa-spinner"></i> Ongoing</label>
                
                <input type="radio" id="completed" name="history-status" value="Completed">
                <label for="completed"><i class="fas fa-check-circle"></i> Completed</label>
                
                <input type="radio" id="cancelled" name="history-status" value="Cancelled">
                <label for="cancelled"><i class="fas fa-times-circle"></i> Cancelled</label>
            </div>
        </div>

        <!-- History Cards Container -->
        <div class="history-cardview-wrapper" id="history-cardview-wrapper"></div>

        <!-- No Reservations Placeholder -->
        <div class="no-reservations" id="no-reservations" style="display: none;">
            <i class="far fa-calendar-times"></i>
            <h3>No reservations found</h3>
            <p>You don't have any reservations in this category yet.</p>
            <a href="nav-reservation.php" class="book-now-btn">Book a Room Now</a>
        </div>
    </main>

    <script>
        const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
    </script>

    <script src="function-logout.js"></script>
    <script src="nav-history.js"></script>
    
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