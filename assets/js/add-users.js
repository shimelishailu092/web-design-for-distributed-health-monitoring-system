document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("addUserForm");

    if (!form) {
        console.error("Add User form not found");
        return;
    }

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        fetch("../api/admin/add-user.php", {
            method: "POST",
            body: new FormData(form)
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            alert(data.message);

            if (data.success) {
                form.reset();
            }
        })
        .catch(err => {
            console.error("Add user error:", err);
            alert("Something went wrong");
        });
    });

});
