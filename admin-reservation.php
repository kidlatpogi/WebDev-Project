<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reserved Rooms</title>
    <link href="https://fonts.googleapis.com/css2?family=Rowdies&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin-reservation.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<nav class="nav">
    <div class="hamburger-menu">
        <input id="menu__toggle" type="checkbox" />
        <label class="menu__btn" for="menu__toggle"><span></span></label>
        <ul class="menu__box">
            <li><a class="menu__item" href="admin-home.html"><i class="fas fa-home"></i> Home</a></li>
            <li><a class="menu__item" href="admin-reservation.php"><i class="fas fa-calendar-check"></i> Reserve Rooms</a></li>
            <li><a class="menu__item" href="admin-logs.html"><i class="fas fa-clipboard-list"></i> Logs</a></li>
            <li><a class="menu__item" href="admin-available-rooms.html"><i class="fas fa-door-open"></i> Available Rooms</a></li>
            <li><a class="menu__item" href="admin-client.html"><i class="fas fa-users"></i> Clients</a></li>
            <li><a class="menu__item" href="admin-archive.html"><i class="fas fa-archive"></i> Archive</a></li>
            <li><a class="menu__item" href="index.html"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="logo">
        <img src="Photos/nu-logo-label.png" alt="NU Logo">
    </div>
</nav>

<div class="main">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Search by room, name, or date...">
    </div>

    <div id="reservationContainer">
        <?php
        $conn = new mysqli("localhost", "root", "", "room_reservation");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "
            SELECT r.reservation_id, r.reservation_date, r.start_time, r.end_time,
                   ud.fname, ud.lname,
                   ro.floor, ro.room_type,
                   rd.description AS room_number,
                   rs.status
            FROM RESERVATION r
            JOIN USER_DETAILS ud ON r.userDetails_id = ud.userDetails_id
            JOIN ROOMS ro ON r.room_id = ro.room_id
            JOIN ROOM_DETAILS rd ON ro.room_id = rd.room_id
            JOIN RESERVATION_STATUS rs ON r.status_id = rs.status_id
            WHERE rs.status = 'Ongoing'
              AND r.reservation_date >= CURDATE()
            ORDER BY r.reservation_date, r.start_time
        ";
        $reservations = $conn->query($sql);

        if ($reservations->num_rows > 0) {
            $currentDate = '';
            while ($row = $reservations->fetch_assoc()) {
                $date = date("F j, Y", strtotime($row['reservation_date']));
                if ($date !== $currentDate) {
                    echo "<div class='date-title'><i class='fas fa-calendar-day'></i> $date</div>";
                    $currentDate = $date;
                }

                preg_match('/\d{3}$/', $row['room_number'], $matches);
                $roomNum = $matches[0] ?? $row['room_number'];

                echo "
                <div class='reservation-card'>
                    <div class='card-header'>
                        <div class='room-number'>Room $roomNum</div>
                        <div class='room-type'>{$row['room_type']}</div>
                    </div>
                    <div class='card-body'>
                        <div class='user-info'>
                            <div class='user-icon'><i class='fas fa-user'></i></div>
                            <div class='user-name'>{$row['fname']} {$row['lname']}</div>
                        </div>
                        <div class='reservation-details'>
                            <div class='detail-item'>
                                <div class='detail-icon'><i class='fas fa-building'></i></div>
                                <div class='detail-content'>
                                    <div class='detail-label'>Floor</div>
                                    <div class='detail-value'>{$row['floor']}</div>
                                </div>
                            </div>
                            <div class='detail-item'>
                                <div class='detail-icon'><i class='fas fa-door-open'></i></div>
                                <div class='detail-content'>
                                    <div class='detail-label'>Room Type</div>
                                    <div class='detail-value'>{$row['room_type']}</div>
                                </div>
                            </div>
                            <div class='detail-item'>
                                <div class='detail-icon'><i class='fas fa-calendar'></i></div>
                                <div class='detail-content'>
                                    <div class='detail-label'>Date</div>
                                    <div class='detail-value'>$date</div>
                                </div>
                            </div>
                            <div class='detail-item'>
                                <div class='detail-icon'><i class='fas fa-clock'></i></div>
                                <div class='detail-content'>
                                    <div class='detail-label'>Time Slot</div>
                                    <div class='time-slot'>
                                        " . date("g:i A", strtotime($row['start_time'])) . " - " . date("g:i A", strtotime($row['end_time'])) . "
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='card-footer'>
                        <div class='status-badge status-ongoing'>
                            <i class='fas fa-circle'></i> {$row['status']}
                        </div>
                        <button class='cancel-btn' onclick='cancelReservation({$row['reservation_id']})'>
                            <i class='fas fa-times'></i> Cancel
                        </button>
                    </div>
                </div>";
            }
        } else {
            echo "<div style='grid-column:1/-1;text-align:center;padding:40px;color:#666;'>
                    <i class='fas fa-calendar-times' style='font-size:3rem;margin-bottom:20px;'></i>
                    <h3>No ongoing reservations found</h3>
                    <p>There are currently no upcoming reservations scheduled.</p>
                  </div>";
        }
        $conn->close();
        ?>
    </div>
</div>

<script>
function filterReservations() {
    const filter = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.reservation-card').forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(filter) ? 'block' : 'none';
    });

    document.querySelectorAll('.date-title').forEach(title => {
        let hasVisibleCards = false;
        let next = title.nextElementSibling;
        while (next && !next.classList.contains('date-title')) {
            if (next.style.display !== 'none') {
                hasVisibleCards = true;
                break;
            }
            next = next.nextElementSibling;
        }
        title.style.display = hasVisibleCards ? 'flex' : 'none';
    });
}
document.getElementById('searchInput').addEventListener('input', filterReservations);

function cancelReservation(id) {
    if (confirm("Are you sure you want to cancel this reservation?")) {
        fetch('cancel_reservation.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ reservation_id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reservation cancelled successfully.');
                location.reload();
            } else {
                alert('Failed to cancel reservation: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while canceling the reservation.');
        });
    }
}
</script>
</body>
</html>
