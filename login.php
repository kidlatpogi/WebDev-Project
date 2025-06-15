<?php
session_start();

$email = $_POST["email"] ?? '';
$password = $_POST["password"] ?? '';

// Basic validation
if (empty($email) || empty($password)) {
    echo "Email and password are required.";
    exit();
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "room_reservation");
if ($conn->connect_error) {
    echo "Database connection failed.";
    exit();
}

// Prepare and execute query to check user
$stmt = $conn->prepare("SELECT user_id, password FROM USERS WHERE email = ? AND is_deleted = 0");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['email'] = $email;

        // Get full name
        $stmt2 = $conn->prepare("SELECT fname, lname FROM USER_DETAILS WHERE user_id = ?");
        $stmt2->bind_param("i", $row['user_id']);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2->num_rows === 1) {
            $row2 = $result2->fetch_assoc();
            $_SESSION['first_name'] = $row2['fname'];
            $_SESSION['last_name'] = $row2['lname'];
        } else {
            $_SESSION['first_name'] = 'User';
            $_SESSION['last_name'] = '';
        }
        $stmt2->close();

        // Check if email matches admin pattern (allowing admin@admin-nu.com and admin123@admin-nu.com)
        if (preg_match('/^admin.*@admin-nu\.com$/i', $email)) {
            echo "admin_success"; // Special response for admin
        } else {
            echo "success"; // Regular user success
        }
        $stmt->close();
        $conn->close();
        exit();
    } else {
        $stmt->close();
        $conn->close();
        echo "Invalid password.";
        exit();
    }
} else {
    $stmt->close();
    $conn->close();
    echo "No account found with that email.";
    exit();
}
?>
