<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// Get first word only from session first and last names
$fname = isset($_SESSION['first_name']) ? explode(' ', trim($_SESSION['first_name']))[0] : null;
$lname = isset($_SESSION['last_name']) ? explode(' ', trim($_SESSION['last_name']))[0] : null;
$email = $_SESSION['email'] ?? '';

if (!$lname) {
    echo json_encode(['error' => 'User last name not found in session']);
    exit();
}

echo json_encode([
    'fname' => $fname,
    'lname' => $lname,
    'email' => $email,
]);
