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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NU Dasma Room Reservation</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="nav-home.css" />
</head>
<body>

  
    <!-- Desktop Navbar -->
    <nav class="desktop-navbar">

    <!--NU Logo -->
    <div class="logo-container">
        <a href="nav-home.php"><img src="Photos/nu-logo-label.png" class="nu-logo"></a>
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
            <li><a class="menu__item active" href="nav-home.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a class="menu__item" href="nav-rooms.php"><i class="fas fa-door-open"></i> Rooms</a></li>
            <li><a class="menu__item" href="nav-reservation.php"><i class="fas fa-calendar-check"></i> Reservation</a></li>
            <li><a class="menu__item" href="nav-account.php"><i class="fas fa-user-circle"></i> Account</a></li>
            <li><a class="menu__item" href="nav-history.php"><i class="fas fa-history"></i> History</a></li>
            <li><a class="menu__item" href="index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <!-- Hamburger Navigation End -->

  <!-- Hero Section -->
  <section class="hero">
    <img src="Photos/nu-logo.png" alt="NU Logo" class="hero-logo" />
    <h1>"Room Reservation System"</h1>
    <p>
      The NU Dasma Room Reservation System, a web project dedicated for students,
      professors, and the like to utilize the Nationalian environment for conferences,
      classrooms, study spaces etc. This half-baked platform are freshly and honorably
      designed by yours truly, the Special Forces.
    </p>

    <div class="image-gallery">
      <img src="Photos/room1.png" alt="Room 1" />
      <img src="Photos/room2.png" alt="Room 2" />
      <img src="Photos/room3.png" alt="Room 3" />
      <img src="Photos/room4.png" alt="Room 4" />
    </div>
  </section>

  <!-- Footer -->
<footer>
  <div class="footer-container">
    
    <!-- Left: Logo -->
    <div class="footer-left">
      <img src="Photos/nu-logo-mono.png" alt="NU Logo" />
    </div>

    <!-- Center: Address and Copyright -->
    <div class="footer-center">
      <p>
        © 2024 National University. All rights reserved.<br>
        Sampaloc 1 Bridge, SM Dasmariñas, Governor's Drive, Dasmariñas, Cavite 4114
      </p>
    </div>

    <!-- Right: Icons and Links -->
    <div class="footer-right">
      <div class="footer-icons">
        <img src="Photos/facebook.png" alt="Facebook" />
        <img src="Photos/instagram.png" alt="Instagram" />
        <img src="Photos/twitter.png" alt="Twitter" />
        <img src="Photos/youtube.png" alt="YouTube" />
      </div>
      <div class="footer-links">
        <a href="#">Support</a> | <a href="#">About Us</a>
      </div>
    </div>

  </div>
</footer>

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