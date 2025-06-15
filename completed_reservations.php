<?php
require_once 'connection.php';

if (!$conn || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

// Optional: Set timezone to match your app's timezone
// $conn->query("SET time_zone = '+00:00'"); // adjust offset if needed

// Get status_ids for 'Ongoing' and 'Completed'
$statusSql = "SELECT status, status_id FROM RESERVATION_STATUS WHERE status IN ('Ongoing', 'Completed')";
$statusResult = $conn->query($statusSql);

$statusIds = [];
while ($row = $statusResult->fetch_assoc()) {
    $statusIds[$row['status']] = intval($row['status_id']);
}

if (!isset($statusIds['Ongoing']) || !isset($statusIds['Completed'])) {
    echo json_encode(['success' => false, 'error' => 'Required statuses not found']);
    exit();
}

// For debugging: Log current MySQL date/time
$nowResult = $conn->query("SELECT NOW() AS now_time, CURDATE() AS cur_date, CURTIME() AS cur_time");
$nowRow = $nowResult->fetch_assoc();
error_log("MySQL NOW(): {$nowRow['now_time']}, CURDATE(): {$nowRow['cur_date']}, CURTIME(): {$nowRow['cur_time']}");

// Check if any reservations need update (for debugging)
$selectSql = "
    SELECT reservation_id, reservation_date, end_time 
    FROM RESERVATION 
    WHERE status_id = ? 
      AND (reservation_date < CURDATE() OR (reservation_date = CURDATE() AND end_time <= CURTIME()))
";
$selectStmt = $conn->prepare($selectSql);
$selectStmt->bind_param('i', $statusIds['Ongoing']);
$selectStmt->execute();
$selectResult = $selectStmt->get_result();
$reservationsToUpdate = $selectResult->num_rows;
error_log("Reservations to update from Ongoing to Completed: $reservationsToUpdate");
$selectStmt->close();

if ($reservationsToUpdate === 0) {
    echo json_encode(['success' => true, 'updated_rows' => 0, 'message' => 'No reservations to update']);
    $conn->close();
    exit();
}

// Update all reservations where date/time passed and status is Ongoing
$updateSql = "
    UPDATE RESERVATION 
    SET status_id = ? 
    WHERE status_id = ? 
      AND (reservation_date < CURDATE() OR (reservation_date = CURDATE() AND end_time <= CURTIME()))
";

$stmt = $conn->prepare($updateSql);
$stmt->bind_param('ii', $statusIds['Completed'], $statusIds['Ongoing']);

if ($stmt->execute()) {
    error_log("Updated reservations count: " . $stmt->affected_rows);
    echo json_encode(['success' => true, 'updated_rows' => $stmt->affected_rows]);
} else {
    error_log("Update failed: " . $stmt->error);
    echo json_encode(['success' => false, 'error' => 'Update failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
