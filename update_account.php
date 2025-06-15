<?php
session_start();
header('Content-Type: application/json');

// Hide errors from the output but still log them
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require 'connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

// Check if required fields are present
if (!isset($_POST['password'], $_POST['new_password'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$old_password = $_POST['password'];
$new_password = $_POST['new_password'];

// Validate password strength (optional)
if (strlen($new_password) < 8) {
    echo json_encode(["success" => false, "message" => "New password must be at least 8 characters long"]);
    exit();
}

// Step 1: Fetch current hashed password
$stmt = $conn->prepare("SELECT password FROM USERS WHERE user_id = ?");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(["success" => false, "message" => "Database error"]);
    exit();
}

$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    echo json_encode(["success" => false, "message" => "Database error"]);
    exit();
}

$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit();
}

$row = $result->fetch_assoc();
$hashed_password = $row['password'];

// Step 2: Verify old password
if (!password_verify($old_password, $hashed_password)) {
    echo json_encode(["success" => false, "message" => "Old password is incorrect"]);
    exit();
}

// Step 3: Hash new password and update
$new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$update_stmt = $conn->prepare("UPDATE USERS SET password = ? WHERE user_id = ?");
if (!$update_stmt) {
    error_log("Prepare failed (update USERS): " . $conn->error);
    echo json_encode(["success" => false, "message" => "Database error"]);
    exit();
}

$update_stmt->bind_param("si", $new_hashed_password, $user_id);
if (!$update_stmt->execute()) {
    error_log("Execute failed (update USERS): " . $update_stmt->error);
    echo json_encode(["success" => false, "message" => "Failed to update password"]);
    exit();
}

echo json_encode(["success" => true, "message" => "Password updated successfully!"]);
exit();
?>
