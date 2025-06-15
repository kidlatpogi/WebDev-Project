<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$full_name = $_SESSION['first_name'] ?? 'User';

// Get first two parts of the full name
$name_parts = explode(' ', $full_name);
$first_two_names = implode(' ', array_slice($name_parts, 0, 2));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Account</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="nav-account.css" />
    
</head>
<body>
    <!-- Desktop Navbar (unchanged) -->
    <nav class="desktop-navbar">
        <div class="logo-container">
            <a href="nav-home.php"><img src="Photos/nu-logo-label-blue.png" class="nu-logo" /></a>
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
                Hi, <?php echo htmlspecialchars($first_two_names); ?>
                <i class="fas fa-chevron-down dropdown-icon"></i>
                <div id="logout-menu">
                    <button onclick="logoutUser()"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hamburger Navigation (unchanged) -->
    <div class="hamburger-menu">
        <input id="menu__toggle" type="checkbox" />
        <label class="menu__btn" for="menu__toggle">
            <span></span>
        </label>
        <ul class="menu__box">
            <li><a class="menu__item" href="nav-home.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a class="menu__item" href="nav-rooms.php"><i class="fas fa-door-open"></i> Rooms</a></li>
            <li><a class="menu__item" href="nav-reservation.php"><i class="fas fa-calendar-check"></i> Reservation</a></li>
            <li><a class="menu__item active" href="nav-account.php"><i class="fas fa-user-circle"></i> Account</a></li>
            <li><a class="menu__item" href="nav-history.php"><i class="fas fa-history"></i> History</a></li>
            <li><a class="menu__item" href="index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Enhanced Account Content -->
    <main class="account-container">
        <div class="account-header">
            <div class="header-decoration">
                <div class="decoration-line"></div>
                <div class="decoration-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="decoration-line"></div>
            </div>
            <h1>My Account</h1>
            <p class="account-subtitle">Manage your profile and security settings</p>
        </div>

        <div class="account-content">
            <!-- Photo Section -->
            <div class="account-photo-section">
                <form id="photo-upload-form" method="POST" enctype="multipart/form-data" onsubmit="return false;">
                    <div class="photo-container">
                        <label for="account-photo-input" class="photo-label">
                            <img id="account-photo-preview" src="show_photo.php" alt="Account Photo" />
                            <div class="photo-overlay">
                                <i class="fas fa-camera"></i>
                                <span>Change Photo</span>
                            </div>
                        </label>
                        <input type="file" id="account-photo-input" name="account_photo" accept="image/*" style="display:none" />
                    </div>
                    <p class="user-name"><?php echo htmlspecialchars($first_two_names); ?></p>
                </form>
            </div>

            <!-- Account Details Form -->
            <form class="account-details-form" id="account-update-form" method="POST">
                <div class="form-notice">
                    <i class="fas fa-info-circle"></i>
                    <span>Contact your Admin to change your Name and Email.</span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="account-fname"><i class="fas fa-user"></i> First Name</label>
                        <input type="text" id="account-fname" name="fname" placeholder="Firstname" required />
                    </div>
                    <div class="form-group">
                        <label for="account-lname"><i class="fas fa-user"></i> Last Name</label>
                        <input type="text" id="account-lname" name="lname" placeholder="Lastname" required />
                    </div>
                </div>

                <div class="form-group">
                    <label for="account-email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="account-email" name="email" placeholder="Email" required />
                </div>

                <div class="form-section-header">
                    <i class="fas fa-lock"></i>
                    <h3>Change Password</h3>
                </div>

                <div class="form-group">
                    <label for="account-password"><i class="fas fa-key"></i> Old Password</label>
                    <input type="password" id="account-password" name="password" placeholder="Old Password" required />
                </div>

                <div class="form-group">
                    <label for="account-new-password"><i class="fas fa-key"></i> New Password</label>
                    <input type="password" id="account-new-password" name="new_password" placeholder="New Password" required />
                </div>

                <div class="form-actions">
                    <button type="submit" class="save-button">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Notification System -->
    <div id="notification" class="notification-hidden">
        <span id="notification-message"></span>
    </div>

    <script src="function-fetch-userDesc-others.js"></script>

    <!-- Photo Preview -->
    <script>
        document.getElementById('account-photo-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            // preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('account-photo-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
            document.getElementById('photo-upload-form').submit();
        }
    });
    </script>

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

    <script src="function-update-account.js"></script>
    <script src="function-logout.js"></script>

    <script>
        // Initialize form with current user data
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch and populate current user data
            fetch('get_user_data.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('account-fname').value = data.fname;
                        document.getElementById('account-lname').value = data.lname;
                        document.getElementById('account-email').value = data.email;
                    }
                })
                .catch(error => {
                    console.error('Error fetching user data:', error);
                });
        });

        // Enhanced photo upload with better error handling
        document.getElementById('account-photo-input').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('account-photo-preview');
            
            if (file) {
                // Validate file
                const validTypes = ['image/jpeg', 'image/png'];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!validTypes.includes(file.type)) {
                    showNotification('Please upload a JPG or PNG image', 'error');
                    return;
                }

                if (file.size > maxSize) {
                    showNotification('Image must be less than 2MB', 'error');
                    return;
                }

                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    
                    // Upload immediately after preview
                    uploadPhoto(file);
                };
                reader.readAsDataURL(file);
            }
        });

        function uploadPhoto(file) {
            const formData = new FormData();
            formData.append('account_photo', file);

            fetch('upload_photo.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(data => {
                if (data === 'success') {
                    showNotification('Profile photo updated successfully', 'success');
                } else {
                    throw new Error(data || 'Unknown error occurred');
                }
            })
            .catch(error => {
                showNotification(error.message, 'error');
                console.error('Upload error:', error);
            });
        }

        // Account update form submission
        document.getElementById('account-update-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            
            // Show loading state
            const saveButton = form.querySelector('.save-button');
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveButton.disabled = true;
            
            fetch('update_account.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Clear password fields on success
                    document.getElementById('account-password').value = '';
                    document.getElementById('account-new-password').value = '';
                    
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                showNotification('An error occurred. Please try again.', 'error');
                console.error('Error:', error);
            })
            .finally(() => {
                saveButton.innerHTML = '<i class="fas fa-save"></i> Save Changes';
                saveButton.disabled = false;
            });
        });

        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            const notificationMsg = document.getElementById('notification-message');
            
            notification.className = `notification-${type}`;
            notificationMsg.textContent = message;
            
            // Animation for showing notification
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.opacity = '1';
            }, 10);
            
            // Hide after 5 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 300);
            }, 5000);
        }
    </script>
</body>
</html>