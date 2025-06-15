document.addEventListener('DOMContentLoaded', function() {
    // DOM elements
    const logsContainer = document.querySelector('.logs-container');
    const searchInput = document.querySelector('.search-box input');
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    // Current filter
    let currentFilter = 'all';
    
    // Fetch and display logs
    function fetchLogs(filter = 'all') {
        fetch(`fetch_logs.php?filter=${filter}`)
            .then(response => response.json())
            .then(data => {
                displayLogs(data);
            })
            .catch(error => {
                console.error('Error:', error);
                logsContainer.innerHTML = `
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Failed to load logs. Please try again later.</p>
                    </div>
                `;
            });
    }
    
    // Display logs in the UI
    function displayLogs(logs) {
        logsContainer.innerHTML = '';
        
        if (logs.length === 0) {
            logsContainer.innerHTML = `
                <div class="no-logs">
                    <i class="fas fa-clipboard"></i>
                    <p>No logs found for the selected filter</p>
                </div>
            `;
            return;
        }
        
        logs.forEach(log => {
            const logEntry = document.createElement('div');
            logEntry.className = `log-entry ${log.status.toLowerCase()}`;
            
            // Format time (12-hour format with AM/PM)
            const formatTime = (timeStr) => {
                const time = new Date(`2000-01-01 ${timeStr}`);
                return time.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            };
            
            // Format log time (from reservation_date)
            const logTime = new Date(`2000-01-01 ${log.log_time}`);
            const formattedLogTime = logTime.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
            
            // Determine icon based on status
            let statusIcon = '';
            if (log.status === 'Completed') statusIcon = 'fa-check-circle';
            else if (log.status === 'Cancelled') statusIcon = 'fa-times-circle';
            else statusIcon = 'fa-calendar-alt'; // For Ongoing
            
            logEntry.innerHTML = `
                <div class="log-time">
                    <span class="time">${formattedLogTime}</span>
                    <i class="fas ${statusIcon}"></i>
                </div>
                <div class="log-details">
                    <h3>Room ${log.room_id} - ${log.room_type}</h3>
                    <div class="log-info">
                        <p><i class="far fa-clock"></i> ${formatTime(log.start_time)} - ${formatTime(log.end_time)}</p>
                        <p><i class="fas fa-user"></i> ${log.fname} ${log.lname}</p>
                    </div>
                    <div class="status-badge">${log.status}</div>
                </div>
            `;
            
            logsContainer.appendChild(logEntry);
        });
    }
    
    // Filter button click handlers
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get filter type from button's class
            currentFilter = this.classList.contains('all') ? 'all' :
                          this.classList.contains('completed') ? 'completed' :
                          this.classList.contains('cancelled') ? 'cancelled' : 'all';
            
            // Fetch logs with new filter
            fetchLogs(currentFilter);
        });
    });
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const logEntries = document.querySelectorAll('.log-entry');
        
        logEntries.forEach(entry => {
            const text = entry.textContent.toLowerCase();
            entry.style.display = text.includes(searchTerm) ? 'flex' : 'none';
        });
    });
    
    // Initial load
    fetchLogs();
});