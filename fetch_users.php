<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "room_reservation";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter parameter from request
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all'; // 'all', 'admins', or 'users'

// Get all non-deleted users and their details
$sql = "SELECT u.user_id, ud.fname, ud.lname, u.email
        FROM USERS u
        INNER JOIN USER_DETAILS ud ON u.user_id = ud.user_id
        WHERE u.is_deleted = 0";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Query failed: " . $conn->error]);
    exit;
}

$users = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Dynamically assign role
        if (preg_match('/^admin.*@admin-nu\.com$/i', $row['email'])) {
            $row['role'] = 'admin';
        } else {
            $row['role'] = 'user';
        }

        // Apply filter
        if (
            $filter === 'all' ||
            ($filter === 'admins' && $row['role'] === 'admin') ||
            ($filter === 'users' && $row['role'] === 'user')
        ) {
            $users[] = $row;
        }
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($users);
?>
