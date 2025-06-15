document.addEventListener("DOMContentLoaded", function () {
  fetch("fetch_users.php")
    .then(response => response.json())
    .then(data => {
      const clientList = document.querySelector(".client-list");
      clientList.innerHTML = ""; // Clear existing content

      data.forEach(user => {
        const card = document.createElement("div");
        card.className = "client-card";
        card.innerHTML = `
          <h3>${user.fname} ${user.lname}</h3>
          <p>${user.email}</p>
          <p>${user.role}</p>
          <button class="edit-btn" data-id="${user.user_id}">Edit</button>
          <button class="delete-btn" data-id="${user.user_id}">Delete</button>
        `;
        clientList.appendChild(card);
      });
    })
    .catch(error => {
      console.error("Error fetching user data:", error);
    });
});

// Delegate click events
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("edit-btn")) {
    const userId = e.target.dataset.id;
    openEditForm(userId);
  }

  if (e.target.classList.contains("delete-btn")) {
    const userId = e.target.dataset.id;
    if (confirm("Are you sure you want to delete this user?")) {
      deleteUser(userId);
    }
  }
});

function deleteUser(id) {
  fetch(`delete_user.php?id=${id}`, { method: "GET" })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
      const button = document.querySelector(`.delete-btn[data-id="${id}"]`);
      if (button) {
        const card = button.closest(".client-card");
        if (card) card.remove();
      }
    })
    .catch(err => {
      console.error("Delete failed:", err);
    });
}

function openEditForm(userId) {
  const card = document.querySelector(`.edit-btn[data-id="${userId}"]`).closest('.client-card');
  const nameParts = card.querySelector("h3").innerText.split(" ");
  const email = card.querySelectorAll("p")[0].innerText;

  document.getElementById("edit-user-id").value = userId;

  document.getElementById("account-fname").value = "";
  document.getElementById("account-fname").placeholder = `Current: ${nameParts[0]}`;

  document.getElementById("account-lname").value = "";
  document.getElementById("account-lname").placeholder = `Current: ${nameParts[1]}`;

  document.getElementById("account-email").value = "";
  document.getElementById("account-email").placeholder = `Current: ${email}`;

  document.getElementById("account-password").value = "";

  document.getElementById("editModal").style.display = "block";
}

function closeEditForm() {
  document.getElementById("editModal").style.display = "none";
}

document.getElementById("editForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const userId = document.getElementById("edit-user-id").value;

  const fnameInput = document.getElementById("account-fname");
  const lnameInput = document.getElementById("account-lname");
  const emailInput = document.getElementById("account-email");
  const passwordInput = document.getElementById("account-password");

  const fname = fnameInput.value.trim() || fnameInput.placeholder.replace("Current: ", "").trim();
  const lname = lnameInput.value.trim() || lnameInput.placeholder.replace("Current: ", "").trim();
  const email = emailInput.value.trim() || emailInput.placeholder.replace("Current: ", "").trim();
  const password = passwordInput.value;

  const formData = new FormData();
  formData.append("user_id", userId);
  formData.append("fname", fname);
  formData.append("lname", lname);
  formData.append("email", email);
  if (password !== "") {
    formData.append("password", password);
  }

  fetch("admin-update_account.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      alert(data.message);
      if (data.success) {
        location.reload();
      }
    })
    .catch(err => {
      console.error("Update failed:", err);
    });
});
