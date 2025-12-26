fetch("../api/admin/get-vitals.php")
.then(res => res.json())
.then(data => {
    const tbody = document.querySelector("#vitalsTable tbody");
    tbody.innerHTML = "";

    data.forEach(v => {
        tbody.innerHTML += `
        <tr>
            <td>${v.id}</td>
            <td>${v.patient}</td>
            <td>${v.heart_rate}</td>
            <td>${v.systolic}/${v.diastolic}</td>
            <td>${v.temperature}</td>
            <td>${v.glucose}</td>
            <td>${v.recorded_at}</td>
        </tr>`;
    });
})
.catch(() => alert("Failed to load vitals"));
