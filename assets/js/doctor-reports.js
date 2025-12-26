/* -------- LOAD PATIENTS -------- */
fetch("../api/doctor/get-all-patients.php")
.then(res => res.json())
.then(data => {
    const select = document.getElementById("patient_id");
    select.innerHTML = '<option value="">Select Patient</option>';

    if (data.success && data.patients.length) {
        data.patients.forEach(p => {
            const opt = document.createElement("option");
            opt.value = p.id;
            opt.textContent = p.full_name;
            select.appendChild(opt);
        });
    } else {
        select.innerHTML = '<option>No patients found</option>';
    }
});

/* -------- UPLOAD REPORT -------- */
document.getElementById("reportForm").addEventListener("submit", e => {
    e.preventDefault();

    const formData = new FormData(e.target);

    fetch("../api/doctor/upload-report.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            e.target.reset();
            loadReports();
        }
    });
});

/* -------- LOAD REPORTS -------- */
function loadReports() {
    fetch("../api/doctor/get-reports.php")
    .then(res => res.json())
    .then(data => {
        const table = document.getElementById("reportTable");
        table.innerHTML = "";

        if (!data.reports.length) {
            table.innerHTML = "<tr><td colspan='4'>No reports uploaded</td></tr>";
            return;
        }

        data.reports.forEach(r => {
            table.innerHTML += `
                <tr>
                    <td>${r.patient_name}</td>
                    <td>${r.title}</td>
                    <td>${r.created_at}</td>
                    <td>
                        <a href="../${r.file_path}" target="_blank">Download</a>
                    </td>
                </tr>
            `;
        });
    });
}

loadReports();
