<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "room_reservation");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

$fname = $mysqli->real_escape_string($_POST['fname']);
$lname = $mysqli->real_escape_string($_POST['lname']);
$floor = $mysqli->real_escape_string($_POST['floor']);
$roomType = $mysqli->real_escape_string($_POST['roomType']);
$room = $mysqli->real_escape_string($_POST['room']);
$date = $mysqli->real_escape_string($_POST['date']);
$starttime = $mysqli->real_escape_string($_POST['starttime']);
$endtime = $mysqli->real_escape_string($_POST['endtime']);
$user_id = $_SESSION['user_id'];

$userDetails_id = null;
$query = "SELECT userDetails_id FROM USER_DETAILS WHERE user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($userDetails_id);
$stmt->fetch();
$stmt->close();

if (!$userDetails_id) {
    $insertUserDetails = "INSERT INTO USER_DETAILS (fname, lname, user_id) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($insertUserDetails);
    $stmt->bind_param("ssi", $fname, $lname, $user_id);
    $stmt->execute();
    $userDetails_id = $stmt->insert_id;
    $stmt->close();
}

$room_id = null;

$room_id = (int)$room;

$queryStatus = "SELECT status_id FROM RESERVATION_STATUS WHERE status = 'Ongoing'";
$result = $mysqli->query($queryStatus);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $status_id = $row['status_id'];
} else {
    $insertStatus = "INSERT INTO RESERVATION_STATUS (status) VALUES ('Ongoing')";
    $mysqli->query($insertStatus);
    $status_id = $mysqli->insert_id;
}

$insertReservation = "INSERT INTO RESERVATION (reservation_date, start_time, end_time, userDetails_id, room_id, status_id) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $mysqli->prepare($insertReservation);
$stmt->bind_param("sssiii", $date, $starttime, $endtime, $userDetails_id, $room_id, $status_id);
$success = $stmt->execute();
$stmt->close();

header('Content-Type: application/json');

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Reservation successful!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => "Error inserting reservation: " . $mysqli->error
    ]);
}


$mysqli->close();
?>
