
<?php
include 'connection.php';

$sql = "SELECT
            r.reservation_id,
            r.reservation_date,
            r.start_time,
            r.end_time,
            ud.fname,
            ud.lname,
            ro.room_id AS room_number,
            ro.room_type,
            rs.status
        FROM
            RESERVATION r
        JOIN
            USER_DETAILS ud ON r.userDetails_id = ud.userDetails_id
        JOIN
            ROOMS ro ON r.room_id = ro.room_id
        JOIN
            RESERVATION_STATUS rs ON r.status_id = rs.status_id
        WHERE
            rs.status = 'Completed'
        ORDER BY
            r.reservation_date DESC, r.start_time DESC";

$result = $conn->query($sql);

$reservations = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
}
$conn->close();

header('Content-Type: application/json');
echo json_encode($reservations);