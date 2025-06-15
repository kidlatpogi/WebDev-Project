<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "room_reservation";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getDayOfWeek($date) {
    return date('l', strtotime($date)); // Returns 'Monday', 'Tuesday', etc.
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = $_POST['room_number'];
    $date = $_POST['date'];
    $day_of_week = getDayOfWeek($date);

    // Skip Sunday
    if ($day_of_week === 'Sunday') {
        echo json_encode([
            "description" => "",
            "available_slots" => []
        ]);
        exit;
    }

    // Get scheduled slots
    $sql = "SELECT start_time, end_time FROM ROOM_SCHEDULE WHERE room_id = ? AND day_of_week = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $room_number, $day_of_week);
    $stmt->execute();
    $result = $stmt->get_result();
    $scheduled_slots = [];
    while ($row = $result->fetch_assoc()) {
        $scheduled_slots[] = [
            "start_time" => date("g:iA", strtotime($row['start_time'])),
            "end_time" => date("g:iA", strtotime($row['end_time']))
        ];
    }

    // Get room type only
    $sql = "SELECT description FROM ROOM_DETAILS WHERE room_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $full_description = $result->fetch_assoc()['description'];
    preg_match('/^(.*?)(?=:)/', $full_description, $matches);
    $room_type = $matches[1] ?? $full_description;

    echo json_encode([
        "description" => $room_type,
        "available_slots" => $scheduled_slots
    ]);
}

$conn->close();
?>
