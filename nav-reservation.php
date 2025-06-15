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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Room Reservation | NU</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="nav-reservation.css" />
</head>
<body>

    <!-- Desktop Navbar (unchanged as requested) -->
    <nav class="desktop-navbar">
        <div class="logo-container">
            <a href="nav-home.php"><img src="Photos/nu-logo-label-blue.png" class="nu-logo"></a>
        </div>
        
        <ul>
            <li><a href="nav-home.php">Home</a></li>
            <li><a href="nav-rooms.php">Rooms</a></li>
            <li><a href="nav-reservation.php">Reservation</a></li>
            <li><a href="nav-account.php">Account</a></li>
            <li><a href="nav-history.php">History</a></li>
        </ul>

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

    <!-- Hamburger Navigation (unchanged as requested) -->
    <div class="hamburger-menu">
        <input id="menu__toggle" type="checkbox" />
        <label class="menu__btn" for="menu__toggle">
            <span></span>
        </label>
        <ul class="menu__box">
            <li><a class="menu__item" href="nav-home.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a class="menu__item" href="nav-rooms.php"><i class="fas fa-door-open"></i> Rooms</a></li>
            <li><a class="menu__item active" href="nav-reservation.php"><i class="fas fa-calendar-check"></i> Reservation</a></li>
            <li><a class="menu__item" href="nav-account.php"><i class="fas fa-user-circle"></i> Account</a></li>
            <li><a class="menu__item" href="nav-history.php"><i class="fas fa-history"></i> History</a></li>
            <li><a class="menu__item" href="index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Enhanced Reservation Section -->
    <main class="reservation-main">
        <div class="reservation-header">
            <div class="header-decoration">
                <div class="decoration-line"></div>
                <div class="decoration-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="decoration-line"></div>
            </div>
            <h1>Room Reservation</h1>
            <p class="reservation-subtitle">Book your perfect study or meeting space</p>
        </div>

        <form action="insert_reservation.php" method="post" class="reservation-form">
            <div class="form-container">
                <!-- Name fields -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="fname"><i class="fas fa-user icon"></i> First Name</label>
                        <input type="text" id="fname" name="fname" placeholder="Enter your first name" required />
                    </div>
                    <div class="form-group">
                        <label for="lname"><i class="fas fa-user icon"></i> Last Name</label>
                        <input type="text" id="lname" name="lname" placeholder="Enter your last name" required />
                    </div>
                </div>

                <!-- Floor, Room Type, Room -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="floor"><i class="fas fa-building icon"></i> Floor</label>
                        <select id="floor" name="floor" required>
                            <option value="" disabled selected>Select floor</option>
                            <option value="4th Floor">4th Floor</option>
                            <option value="5th Floor">5th Floor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="roomType"><i class="fas fa-door-open icon"></i> Room Type</label>
                        <select id="roomType" name="roomType" required>
                            <option value="" disabled selected>Select room type</option>
                            <option value="Lecture Room">Lecture Room</option>
                            <option value="Laboratory Room">Laboratory Room</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="room"><i class="fas fa-map-marker-alt icon"></i> Room</label>
                        <select id="room" name="room" required>
                            <option value="" disabled selected>Select room</option>
                        </select>
                    </div>
                </div>

                <!-- Date, Start Time, End Time -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="date"><i class="fas fa-calendar-day icon"></i> Date</label>
                        <input type="date" id="date" name="date" required />
                    </div>
                    <div class="form-group">
                        <label for="starttime"><i class="fas fa-clock icon"></i> Start Time</label>
                        <select id="starttime" name="starttime" required>
                            <option value="" disabled selected>Select start time</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="endtime"><i class="fas fa-clock icon"></i> End Time</label>
                        <select id="endtime" name="endtime" required>
                            <option value="" disabled selected>Select end time</option>
                        </select>
                    </div>
                </div>

                <!-- Submit button -->
                <div class="form-submit">
                    <button type="submit" class="reserve-btn">
                        <i class="fas fa-bookmark"></i> Reserve Now
                    </button>
                </div>
            </div>
        </form>
    </main>

    <!-- Notification System -->
    <div id="notification" class="notification-hidden">
        <span id="notification-message"></span>
    </div>

    <script src="nav-reservation.js"></script>
    <script src="nav-reservation-time.js"></script>
    <script src="function-fetch-userDesc.js"></script>
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

    <script>
    document.querySelector('.reservation-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const notification = document.getElementById('notification');
        const notificationMsg = document.getElementById('notification-message');

        try {
            const response = await fetch('insert_reservation.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.text();

            if (result.includes("success")) {
                notification.className = 'notification-success';
                notificationMsg.textContent = 'Reservation successful! Redirecting...';
                setTimeout(() => {
                    window.location.href = "nav-history.php";
                }, 2000);
            } else {
                notification.className = 'notification-error';
                notificationMsg.textContent = result || 'Error in reservation';
            }
            
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.opacity = '1';
            }, 10);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 300);
            }, 5000);
            
        } catch (error) {
            notification.className = 'notification-error';
            notificationMsg.textContent = 'Network error: ' + error.message;
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.opacity = '1';
            }, 10);
        }
    });
    </script>
</body>
</html>