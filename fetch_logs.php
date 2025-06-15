<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "room_reservation";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get filter parameter if provided
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    
    // Base query
$query = "SELECT r.reservation_id, r.reservation_date, r.start_time, r.end_time,
                 r.room_id, -- ✅ this fixes the undefined issue
                 ud.fname, ud.lname,
                 ro.floor, ro.room_type,
                 rs.status,
                 TIME(r.reservation_date) as log_time
          FROM RESERVATION r
          JOIN USER_DETAILS ud ON r.userDetails_id = ud.userDetails_id
          JOIN ROOMS ro ON r.room_id = ro.room_id
          JOIN RESERVATION_STATUS rs ON r.status_id = rs.status_id";

    
    // Add filter conditions
    switch($filter) {
        case 'completed':
            $query .= " WHERE rs.status = 'Completed'";
            break;
        case 'cancelled':
            $query .= " WHERE rs.status = 'Cancelled'";
            break;
        case 'ongoing':
            $query .= " WHERE rs.status = 'Ongoing'";
            break;
        // 'all' shows everything
    }
    
    $query .= " ORDER BY r.reservation_date DESC, r.start_time DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($results);
    
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>