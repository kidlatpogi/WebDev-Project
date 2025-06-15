document.addEventListener("DOMContentLoaded", function () {
  const floorSelect = document.getElementById("floor");
  const roomTypeSelect = document.getElementById("roomType");
  const roomSelect = document.getElementById("room");

  function fetchRooms() {
    const floor = floorSelect.value;      // e.g. "4th Floor"
    const type = roomTypeSelect.value;    // e.g. "Lecture Room"

    if (!floor || !type) {
      roomSelect.innerHTML = '<option value="">Select a floor and room type first</option>';
      return;
    }

    // Fetch rooms from PHP API with query params floor and type
    fetch(`get_rooms.php?floor=${encodeURIComponent(floor)}&type=${encodeURIComponent(type)}`)
      .then(response => response.json())
      .then(data => {
        roomSelect.innerHTML = ""; // Clear old options

        if (data.length === 0) {
          roomSelect.innerHTML = '<option value="">No rooms available</option>';
          return;
        }

        // Populate room dropdown with returned rooms (only room_id)
        data.forEach(room => {
          const option = document.createElement("option");
          option.value = room.room_id;
          option.textContent = `Room ${room.room_id}`;
          roomSelect.appendChild(option);
        });
      })
      .catch(err => {
        console.error("Error fetching rooms:", err);
        roomSelect.innerHTML = '<option value="">Error loading rooms</option>';
      });
  }

  // Listen for changes on floor and room type selects
  floorSelect.addEventListener("change", fetchRooms);
  roomTypeSelect.addEventListener("change", fetchRooms);

  // Initial fetch to populate rooms based on default selections
  fetchRooms();
});
