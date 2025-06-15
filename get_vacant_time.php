<?php
header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'room_reservation';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : null;
$date = $_GET['date'] ?? null;

if (!$room_id || !$date) {
    echo json_encode(['error' => 'Missing room_id or date']);
    exit;
}

$day_of_week = date('l', strtotime($date));
if (!$day_of_week) {
    echo json_encode(['error' => 'Invalid date format']);
    exit;
}

// Get room schedule blocks marked as occupied
$sql1 = "SELECT start_time, end_time FROM ROOM_SCHEDULE 
         WHERE room_id = ? AND day_of_week = ? AND is_occupied = TRUE";
$stmt1 = $conn->prepare($sql1);
$stmt1->bind_param("is", $room_id, $day_of_week);
$stmt1->execute();
$res1 = $stmt1->get_result();

$sql2 = "SELECT start_time, end_time FROM RESERVATION 
         WHERE room_id = ? AND reservation_date = ? AND status_id != 2";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("is", $room_id, $date);
$stmt2->execute();
$res2 = $stmt2->get_result();


$times = [];

// Merge both sources into occupied times
while ($row = $res1->fetch_assoc()) {
    $times[] = [
        'start' => substr($row['start_time'], 0, 5),
        'end' => substr($row['end_time'], 0, 5)
    ];
}
while ($row = $res2->fetch_assoc()) {
    $times[] = [
        'start' => substr($row['start_time'], 0, 5),
        'end' => substr($row['end_time'], 0, 5)
    ];
}

echo json_encode($times);
?>
