document.addEventListener("DOMContentLoaded", fetchUsers);

function fetchUsers() {
  fetch("../api/admin/get-users.php")
    .then(res => res.json())
    .then(data => {
      const tbody = document.querySelector("#usersTable tbody");
      tbody.innerHTML = "";

      data.forEach(user => {
        tbody.innerHTML += `
          <tr>
            <td>${user.id}</td>
            <td>${user.full_name}</td>
            <td>${user.email}</td>
            <td>${user.role}</td>
            <td>${user.status}</td>
            <td>
              <a href="edit-user.html?id=${user.id}">Edit</a>
              |
              <a href="#" onclick="deleteUser(${user.id})" style="color:red;">Delete</a>
            </td>
          </tr>
        `;
      });
    });
}

function deleteUser(id) {
  if (!confirm("Are you sure?")) return;

  fetch("../api/admin/delete-user.php?id=" + id)
    .then(res => res.json())
    .then(result => {
      alert(result.message);
      fetchUsers();
    });
}
