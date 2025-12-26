document.addEventListener("DOMContentLoaded", () => {
    const addForm = document.getElementById("userForm");
    const tbody = document.querySelector("#usersTable tbody");

    loadUsers();

    // ======================
    // LOAD USERS
    // ======================
    function loadUsers() {
        fetch("../api/admin/get-users.php")
            .then(res => res.json())
            .then(users => {
                tbody.innerHTML = "";

                users.forEach(u => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${u.id}</td>
                            <td>${u.full_name}</td>
                            <td>${u.email}</td>
                            <td>${u.role}</td>
                            <td>${u.dob}</td>
                            <td>${u.phone}</td>
                            <td>${u.status}</td>
                            <td>${u.created_at}</td>
                             

                            <td>
                                <button onclick="editUser(${u.id})">Edit</button>
                                <button onclick="deleteUser(${u.id})" style="color:red">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    `;
                });
            })
            .catch(err => console.error("Load users error:", err));
    }

    // ======================
    // GLOBAL FUNCTIONS
    // ======================

    // 👉 EDIT USER
    window.editUser = function(id) {
        window.location.href = `edit-user.html?id=${id}`;
    };

    // 👉 DELETE USER
    window.deleteUser = function(id) {
        if (!confirm("Are you sure you want to delete this user?")) return;

        fetch(`../api/admin/delete-user.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                loadUsers();
            })
            .catch(err => console.error("Delete user error:", err));
    };
});
