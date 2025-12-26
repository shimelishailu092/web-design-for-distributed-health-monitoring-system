fetch("../api/admin/get-alerts.php")
.then(res => res.json())
.then(data => {
    const tbody = document.querySelector("#alertsTable tbody");
    tbody.innerHTML = "";

    data.forEach(a => {
        tbody.innerHTML += `
        <tr>
            <td>${a.id}</td>
            <td>${a.patient}</td>
            <td>${a.message}</td>
            <td>${a.status}</td>
            <td>${a.created_at}</td>
        </tr>`;
    });
})
.catch(() => alert("Failed to load alerts"));
