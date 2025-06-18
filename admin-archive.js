document.addEventListener('DOMContentLoaded', () => {
    fetch('get-archive-data.php')
        .then(response => response.json())
        .then(reservations => {
            const table = document.getElementById('archiveTable');
            if (!reservations.length) {
                table.innerHTML = '<p style="text-align: center; margin-top: 30px;">No completed reservations found.</p>';
                return;
            }
            let html = '';
            reservations.forEach((reservation, idx) => {
                const dateReserved = new Date(reservation.reservation_date).toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' });
                const dateCompleted = reservation.completed_date
                    ? new Date(reservation.completed_date).toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' })
                    : dateReserved;
                html += `
                  <div class="archive-item" data-index="${idx}">
                    <div class="archive-item-col">${'ROOM ' + reservation.room_number}</div>
                    <div class="archive-item-col">${dateReserved}</div>
                    <div class="archive-item-col">${dateCompleted}</div>
                  </div>
                `;
            });
            table.innerHTML = html;

            // Add click event to each row
            document.querySelectorAll('.archive-item').forEach(row => {
                row.addEventListener('click', function () {
                    const idx = this.getAttribute('data-index');
                    showModal(reservations[idx]);
                });
            });
        })
        .catch(err => {
            document.getElementById('archiveTable').innerHTML = '<p style="color:red;">Failed to load data.</p>';
            console.error('Error fetching archive data:', err);
        });

    // Modal logic
    const modalOverlay = document.getElementById('modal-overlay');
    const modalDetails = document.getElementById('modal-details');
    const closeModal = document.getElementById('close-modal');
    const mainContent = document.querySelector('.main-content-wrapper');

    function showModal(reservation) {
        if (!reservation) return;
        const dateReserved = new Date(reservation.reservation_date).toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' });
        const dateCompleted = reservation.completed_date
            ? new Date(reservation.completed_date).toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' })
            : dateReserved;
        modalDetails.innerHTML = `
            <span class="modal-label">ROOM NUMBER:</span><span class="modal-value">${reservation.room_number}</span>
            <span class="modal-label">DATE RESERVED:</span><span class="modal-value">${dateReserved}</span>
            <span class="modal-label">DATE COMPLETED:</span><span class="modal-value">${dateCompleted}</span>
            <span class="modal-label">ROOM TYPE:</span><span class="modal-value">${reservation.room_type}</span>
            <span class="modal-label">RESERVED BY:</span><span class="modal-value">${reservation.fname} ${reservation.lname}</span>
        `;
        modalOverlay.classList.add('active');
        mainContent.classList.add('blurred-background');
    }

    closeModal.onclick = function () {
        modalOverlay.classList.remove('active');
        mainContent.classList.remove('blurred-background');
    };

    modalOverlay.onclick = function (e) {
        if (e.target === modalOverlay) {
            modalOverlay.classList.remove('active');
            mainContent.classList.remove('blurred-background');
        }
    };
});
