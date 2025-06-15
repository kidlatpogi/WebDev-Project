<?php
header('Content-Type: application/json'); // Always return JSON header

$host = "localhost";
$user = "root";
$password = "";
$dbname = "room_reservation";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Validate that floor and type parameters exist and sanitize them
if (!isset($_GET['floor']) || !isset($_GET['type'])) {
    // Return empty array if no parameters provided (or you could return error JSON)
    echo json_encode([]);
    exit;
}

$floor = $_GET['floor'];
$type = $_GET['type'];

// Map the inputs to ENUM values expected by DB
$floorEnum = '';
if ($floor === "4th Floor") {
    $floorEnum = "4th";
} elseif ($floor === "5th Floor") {
    $floorEnum = "5th";
}

$typeEnum = '';
if ($type === "Lecture Room") {
    $typeEnum = "Lecture";
} elseif ($type === "Laboratory Room") {
    $typeEnum = "Laboratory";
}

// If invalid floor or type, return empty JSON array
if (empty($floorEnum) || empty($typeEnum)) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT r.room_id, IFNULL(rd.description, 'No description available') AS description
    FROM ROOMS r
    LEFT JOIN ROOM_DETAILS rd ON r.room_id = rd.room_id
    WHERE r.floor = ? AND r.room_type = ?
    ORDER BY r.room_id ASC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "SQL prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("ss", $floorEnum, $typeEnum);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "SQL execute failed: " . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

$result = $stmt->get_result();

$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = [
        'room_id' => $row['room_id'],
        'description' => $row['description']
    ];
}

echo json_encode($rooms);

$stmt->close();
$conn->close();
?>