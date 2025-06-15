<?php
header('Content-Type: application/json');

// Check if user_id and status are provided via GET
if (!isset($_GET['user_id']) || !isset($_GET['status'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing user_id or status parameter."]);
    exit();
}

$userDetailsId = intval($_GET['user_id']);
$status = trim($_GET['status']);

// ✅ Use external connection file
require_once 'connection.php';

// Check if connection is valid
if (!$conn || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}

// Query
$sql = "
    SELECT 
        r.reservation_id,
        DATE_FORMAT(r.reservation_date, '%m/%d/%Y') AS reservation_date,
        TIME_FORMAT(r.start_time, '%h:%i %p') AS start_time,
        TIME_FORMAT(r.end_time, '%h:%i %p') AS end_time,
        ro.room_id,
        ro.floor,
        ro.room_type,
        rs.status
    FROM RESERVATION r
    JOIN ROOMS ro ON r.room_id = ro.room_id
    JOIN RESERVATION_STATUS rs ON r.status_id = rs.status_id
    WHERE rs.status = ?
      AND r.userDetails_id = ?
    ORDER BY r.reservation_date DESC, r.start_time ASC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
    exit();
}

$stmt->bind_param("si", $status, $userDetailsId);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
while ($row = $result->fetch_assoc()) {
    $reservations[] = [
        "reservation_id" => $row["reservation_id"],
        "status" => $row["status"],
        "room" => "Room " . $row["room_id"],
        "type" => $row["room_type"],
        "time" => $row["start_time"] . " - " . $row["end_time"],
        "date" => $row["reservation_date"]
    ];
}

$stmt->close();
$conn->close();

echo json_encode($reservations);
?>
