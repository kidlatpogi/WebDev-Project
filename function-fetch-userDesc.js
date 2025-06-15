document.addEventListener("DOMContentLoaded", () => {
  fetch('get_user_description.php', {
    credentials: 'include' // send session cookies
  })
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      console.error("User fetch error:", data.error);
      return;
    }

    const userDropdown = document.getElementById('user-dropdown');
    if (userDropdown) {
      userDropdown.firstChild.textContent = `Hi, ${data.fname} `;
    }

    const fnameInput = document.getElementById('fname');
    const lnameInput = document.getElementById('lname');

    if (fnameInput) {
      fnameInput.value = data.fname;
      fnameInput.readOnly = true;
    }
    if (lnameInput) {
      lnameInput.value = data.lname;
      lnameInput.readOnly = true;
    }
  })
  .catch(error => {
    console.error("Fetch failed:", error);
  });
});
