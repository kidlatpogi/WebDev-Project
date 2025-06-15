document.addEventListener("DOMContentLoaded", () => {
  fetch('get_user_description.php', {
    credentials: 'include' // send session cookies
  })
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      console.error("User fetch error:", data.error);
    } else {
      // Extract first two words from fname
      const firstTwoNames = data.fname.split(' ').slice(0, 2).join(' ');

      // Update user dropdown greeting with first two names
      const userDropdown = document.getElementById('user-dropdown');
      if (userDropdown) {
        userDropdown.firstChild.textContent = `Hi, ${firstTwoNames} `;
      }

      const fnameInput = document.getElementById('account-fname');
      const lnameInput = document.getElementById('account-lname');
      const emailInput = document.getElementById('account-email');

      if (fnameInput) {
        fnameInput.value = data.fname;
        fnameInput.readOnly = true;
      }
      if (lnameInput) {
        lnameInput.value = data.lname;
        lnameInput.readOnly = true;
      }
      if (emailInput) {
        emailInput.value = data.email;
        emailInput.readOnly = true;
      }
    }
  })
  .catch(error => {
    console.error("Fetch failed:", error);
  });
});
