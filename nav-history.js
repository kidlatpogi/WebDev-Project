document.addEventListener("DOMContentLoaded", function () {
    // First update completed reservations
    fetch('completed_reservations.php')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                console.log(`Completed reservations update success. Rows updated: ${result.updated_rows}`);
                if (result.message) {
                    console.log(`Message: ${result.message}`);
                }
            } else {
                console.error("Failed to update completed reservations:", result.error);
            }
            // Then load initial data
            fetchHistoryData("Ongoing");
        })
        .catch(error => {
            console.error("Failed to update completed reservations:", error);
            fetchHistoryData("Ongoing");
        });

    // Set up radio button event listeners
    document.querySelectorAll('input[name="history-status"]').forEach(radio => {
        radio.addEventListener("change", function () {
            fetchHistoryData(this.value);
        });
    });
});

function fetchHistoryData(status) {
    fetch(`get_reservation_history.php?user_id=${userId}&status=${status}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showError(data.error);
                return;
            }
            renderHistoryCards(status, data);
        })
        .catch(error => {
            console.error("Fetch error:", error);
            showError(`Failed to load ${status} reservations.`);
        });
}

function renderHistoryCards(status, data) {
    const wrapper = document.getElementById("history-cardview-wrapper");
    wrapper.innerHTML = "";

    if (!data || data.length === 0) {
        showNoReservations(status);
        return;
    }

    // Sort data by room name, then time, then date
    data.sort((a, b) => {
        if (a.room.toLowerCase() < b.room.toLowerCase()) return -1;
        if (a.room.toLowerCase() > b.room.toLowerCase()) return 1;
        if (a.time < b.time) return -1;
        if (a.time > b.time) return 1;
        if (a.date < b.date) return -1;
        if (a.date > b.date) return 1;
        return 0;
    });

    data.forEach(item => {
        const card = document.createElement("div");
        card.className = `history-card ${item.status.toLowerCase()}`;
        
        card.innerHTML = `
            <p class="history-status ${item.status.toLowerCase()}">
                <i class="fas ${getStatusIcon(item.status)}"></i> ${item.status}
            </p>
            <h2 class="history-title">${item.room} | ${item.type}</h2>
            <p class="history-time"><i class="far fa-clock"></i> ${formatTime(item.time)}</p>
            <p class="history-date"><i class="far fa-calendar-alt"></i> ${formatDate(item.date)}</p>
            ${item.status === "Ongoing" ? `
            <button class="cancel-btn" data-id="${item.reservation_id}">
                <i class="fas fa-times"></i> Cancel Reservation
            </button>` : ''}
        `;

        if (item.status === "Ongoing") {
            const cancelBtn = card.querySelector(".cancel-btn");
            cancelBtn.addEventListener("click", () => {
                if (confirm("Are you sure you want to cancel this reservation?")) {
                    cancelReservation(item.reservation_id, () => {
                        fetchHistoryData("Cancelled");
                    });
                }
            });
        }

        wrapper.appendChild(card);
    });
}

function getStatusIcon(status) {
    const icons = {
        "Ongoing": "fa-spinner",
        "Completed": "fa-check-circle",
        "Cancelled": "fa-times-circle"
    };
    return icons[status] || "fa-info-circle";
}

function formatTime(timeString) {
    // Format time as HH:MM AM/PM
    const [hours, minutes] = timeString.split(':');
    const period = hours >= 12 ? 'PM' : 'AM';
    const formattedHours = hours % 12 || 12;
    return `${formattedHours}:${minutes} ${period}`;
}

function formatDate(dateString) {
    // Format date as Month Day, Year
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

function showNoReservations(status) {
    const wrapper = document.getElementById("history-cardview-wrapper");
    wrapper.innerHTML = `
        <div class="no-reservations">
            <i class="far fa-calendar-times"></i>
            <h3>No ${status} Reservations</h3>
            <p>You don't have any ${status.toLowerCase()} reservations yet.</p>
            ${status === "Ongoing" ? '<a href="nav-reservation.php" class="book-now-btn">Book a Room Now</a>' : ''}
        </div>
    `;
}

function showError(message) {
    const wrapper = document.getElementById("history-cardview-wrapper");
    wrapper.innerHTML = `
        <div class="history-card error">
            <p class="history-status error"><i class="fas fa-exclamation-triangle"></i> Error</p>
            <p>${message}</p>
        </div>
    `;
}

function cancelReservation(reservationId, callback = null) {
    fetch('cancel_reservation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ reservation_id: reservationId })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert("Reservation cancelled successfully.");
            if (typeof callback === "function") {
                document.querySelector('input[value="Cancelled"]').checked = true;
                callback();
            } else {
                fetchHistoryData("Ongoing");
            }
        } else {
            alert("Failed to cancel reservation. " + (result.error || ""));
            console.error("Cancel error detail:", result.error);
        }
    })
    .catch(error => {
        console.error("Cancel fetch error:", error);
        alert("Error cancelling reservation.");
    });
}