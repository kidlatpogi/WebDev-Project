// Toggle logout menu
document.addEventListener("DOMContentLoaded", function() {
    const userDropdown = document.getElementById("user-dropdown");
    const logoutMenu = document.getElementById("logout-menu");
    if (userDropdown && logoutMenu) {
        userDropdown.onclick = function(e) {
            e.stopPropagation();
            logoutMenu.style.display = logoutMenu.style.display === "block" ? "none" : "block";
        };
        document.body.onclick = function() {
            logoutMenu.style.display = "none";
        };
    }
});

function logoutUser() {
    window.location.href = "index.html";
}