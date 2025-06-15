<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "room_reservation");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$user_id = intval($_POST['user_id'] ?? 0);
if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid user ID.']);
    exit;
}

$query = "SELECT u.email, u.password, ud.fname, ud.lname 
          FROM USERS u LEFT JOIN USER_DETAILS ud ON u.user_id = ud.user_id 
          WHERE u.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$currentData = $result->fetch_assoc();
$stmt->close();

if (!$currentData) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit;
}

$updatedFields = [];

// Email update
if (!empty($_POST['email']) && $_POST['email'] !== $currentData['email']) {
    $email = $_POST['email'];
    $check = $conn->prepare("SELECT user_id FROM USERS WHERE email = ? AND user_id != ?");
    $check->bind_param("si", $email, $user_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already in use.']);
        exit;
    }
    $check->close();

    $stmt = $conn->prepare("UPDATE USERS SET email = ? WHERE user_id = ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $stmt->close();
    $updatedFields[] = "email";
}

// Password update
if (!empty($_POST['new_password'])) {
    if (empty($_POST['password']) || !password_verify($_POST['password'], $currentData['password'])) {
        echo json_encode(['success' => false, 'message' => 'Incorrect current password.']);
        exit;
    }

    $new_hashed = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE USERS SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_hashed, $user_id);
    $stmt->execute();
    $stmt->close();
    $updatedFields[] = "password";
}

// First name
if (!empty($_POST['fname']) && $_POST['fname'] !== $currentData['fname']) {
    $fname = $_POST['fname'];
    $stmt = $conn->prepare("UPDATE USER_DETAILS SET fname = ? WHERE user_id = ?");
    $stmt->bind_param("si", $fname, $user_id);
    $stmt->execute();
    $stmt->close();
    $updatedFields[] = "first name";
}

// Last name
if (!empty($_POST['lname']) && $_POST['lname'] !== $currentData['lname']) {
    $lname = $_POST['lname'];
    $stmt = $conn->prepare("UPDATE USER_DETAILS SET lname = ? WHERE user_id = ?");
    $stmt->bind_param("si", $lname, $user_id);
    $stmt->execute();
    $stmt->close();
    $updatedFields[] = "last name";
}

if (empty($updatedFields)) {
    echo json_encode(['success' => false, 'message' => 'No changes were made.']);
} else {
    echo json_encode(['success' => true, 'message' => 'Updated: ' . implode(", ", $updatedFields) . '.']);
}

$conn->close();
?>
