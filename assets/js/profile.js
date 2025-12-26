document.addEventListener("DOMContentLoaded", () => {
    fetch("../../api/patient/get-profile.php")
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message);
                return;
            }

            document.getElementById("full_name").value = data.profile.full_name;
            document.getElementById("email").value = data.profile.email;
            document.getElementById("phone").value = data.profile.phone;
        });
});

function updateProfile() {
    const payload = {
        full_name: document.getElementById("full_name").value,
        email: document.getElementById("email").value,
        phone: document.getElementById("phone").value
    };

    fetch("../../api/patient/update-profile.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => alert(data.message));
}

function changePassword() {
    const payload = {
        current_password: document.getElementById("current_password").value,
        new_password: document.getElementById("new_password").value,
        confirm_password: document.getElementById("confirm_password").value
    };

    fetch("../../api/patient/change-password.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => alert(data.message));
}
