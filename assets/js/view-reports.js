fetch("../api/admin/get-reports.php")
.then(res => res.json())
.then(data => {

    const tbody = document.querySelector("#reportsTable tbody");
    tbody.innerHTML = "";

    // If backend returned error
    if (data.success === false) {
        console.error(data.error);
        tbody.innerHTML = `<tr><td colspan="6">Server error</td></tr>`;
        return;
    }

    // No reports
    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6">No reports found</td></tr>`;
        return;
    }

    // Display reports
    data.forEach(r => {
        tbody.innerHTML += `
        <tr>
            <td>${r.id}</td>
            <td>${r.patient ?? 'N/A'}</td>
            <td>${r.doctor ?? 'N/A'}</td>
            <td>${r.title}</td>
            <td>
                <a href="../${r.file_path}" target="_blank">Download</a>
            </td>
            <td>${r.created_at}</td>
        </tr>
        `;
    });
})
.catch(err => {
    console.error(err);
    alert("Failed to load reports");
});
