document.addEventListener("DOMContentLoaded", function () {
    const roomGrid = document.getElementById("room-grid");

    // Random image for 501–509
    function getFixedImageFor501to509() {
        const options = ["501.jpg", "507.jpg", "508.jpg"];
        const chosen = options[Math.floor(Math.random() * options.length)];
        return `Photos/TYPEOFROOMS/${chosen}`;
    }

    function getRoomImage(roomId) {
        const idNum = parseInt(roomId, 10);
        if (idNum >= 501 && idNum <= 509) {
            return getFixedImageFor501to509();
        }
        return `Photos/TYPEOFROOMS/${roomId}.jpg`;
    }

    function renderRooms() {
        const floor = document.querySelector('input[name="floor"]:checked')?.value;
        const type = document.querySelector('input[name="room-type"]:checked')?.value;

        if (!floor || !type) return;

        fetch(`get_rooms.php?floor=${encodeURIComponent(floor)}&type=${encodeURIComponent(type)}`)
            .then(response => {
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return response.json();
                }
                return response.text().then(text => Promise.reject(new Error(text)));
            })
            .then(rooms => {
                roomGrid.innerHTML = "";

                let row;
                rooms.forEach(function (room, idx) {
                    const isMobile = window.innerWidth <= 700;
                    const perRow = isMobile ? 1 : 5;

                    if (idx % perRow === 0) {
                        row = document.createElement("div");
                        row.style.display = "flex";
                        row.style.justifyContent = "center";
                        row.style.marginBottom = "20px";
                        roomGrid.appendChild(row);
                    }

                    const cell = document.createElement("div");
                    cell.classList.add("room-cell");
                    cell.style.width = "220px";
                    cell.style.margin = "0 15px";
                    cell.style.textAlign = "center";
                    cell.style.cursor = "pointer";
                    cell.style.display = "flex";
                    cell.style.flexDirection = "column";
                    cell.style.alignItems = "center";

                    // Click to select room
                    cell.onclick = function () {
                        alert(`You clicked Room ${room.room_id} (${type})`);
                    };

                    // Image
                    const img = document.createElement("img");
                    img.src = getRoomImage(room.room_id);
                    img.alt = "Room " + room.room_id;
                    img.style.width = "200px";
                    img.style.height = "200px";
                    img.style.objectFit = "cover";
                    img.style.display = "block";
                    img.style.margin = "0 auto 20px auto";

                    // Fallback for failed image
                    img.onerror = function () {
                        const hasLecture = room.description?.toLowerCase().includes("lecture");
                        if (hasLecture) {
                            const rand = Math.floor(Math.random() * 6) + 1;
                            this.src = `Photos/TYPEOFROOMS/LECTURE_ANGLE_${rand}.jpg`;
                        } else {
                            this.src = "photos/room1.png";
                        }
                    };

                    // Modal preview
                    img.onclick = function (e) {
                        e.stopPropagation();
                        const modal = document.getElementById("photo-modal");
                        const modalImg = document.getElementById("modal-img");
                        const modalDesc = document.getElementById("modal-desc");
                        modalImg.src = img.src;
                        modalDesc.textContent = room.description || "No description available.";
                        modal.style.display = "flex";
                    };
                    cell.appendChild(img);

                    // Label
                    const label = document.createElement("div");
                    label.textContent = "Room " + room.room_id;
                    label.style.marginTop = "0";
                    label.style.color = "white";
                    cell.appendChild(label);

                    row.appendChild(cell);
                });
            })
            .catch(error => {
                console.error("Error fetching rooms:", error);
                roomGrid.innerHTML = `<p style="color: red; text-align: center;">${error.message}</p>`;
            });
    }

    // Event listeners
    document.querySelectorAll('input[name="floor"], input[name="room-type"]').forEach(radio => {
        radio.addEventListener("change", renderRooms);
    });

    // Modal close
    const modal = document.getElementById("photo-modal");
    if (modal) {
        modal.onclick = function (e) {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        };
    }

    // Responsive re-render
    let lastIsMobile = window.innerWidth <= 700;
    window.addEventListener("resize", function () {
        const isMobile = window.innerWidth <= 700;
        if (isMobile !== lastIsMobile) {
            lastIsMobile = isMobile;
            renderRooms();
        }
    });

    renderRooms();
});
