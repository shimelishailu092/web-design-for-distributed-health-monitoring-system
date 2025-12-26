const params = new URLSearchParams(window.location.search);
const userId = params.get("id");

if (!userId) {
  alert("User ID missing");
  window.location.href = "manage-users.html";
}

// Load user data
fetch(`../api/admin/get-user.php?id=${userId}`)
  .then(res => res.json())
  .then(user => {
    document.getElementById("user_id").value = user.id;
    document.getElementById("full_name").value = user.full_name;
    document.getElementById("email").value = user.email;
    document.getElementById("role").value = user.role;
    document.getElementById("status").value = user.status;
  });

// Update user
document.getElementById("editUserForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const data = {
    id: document.getElementById("user_id").value,
    full_name: document.getElementById("full_name").value,
    email: document.getElementById("email").value,
    role: document.getElementById("role").value,
    status: document.getElementById("status").value
  };

  fetch("../api/admin/update-user.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  })
  .then(res => res.json())
  .then(result => {
    alert(result.message);
    window.location.href = "manage-users.html";
  });
});
