<?php
$firstName = $_POST['firstname'];
$lastName = $_POST['lastname'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirm_password'];

// Check if passwords match
if ($password !== $confirmPassword) {
    die('Passwords do not match.');
}

// Hash the password securely
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'room_reservation');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Insert into USERS table
$stmt1 = $conn->prepare("INSERT INTO USERS(email, password) VALUES(?, ?)");
$stmt1->bind_param("ss", $email, $hashedPassword);
$stmt1->execute();

// Get the inserted user_id from USERS
$user_id = $conn->insert_id;

// Insert into USER_DETAILS table
$stmt2 = $conn->prepare("INSERT INTO USER_DETAILS(fname, lname, user_id) VALUES(?, ?, ?)");
$stmt2->bind_param("ssi", $firstName, $lastName, $user_id);
$stmt2->execute();

$stmt1->close();
$stmt2->close();
$conn->close();

header("Location: index.html");
exit();
?>
