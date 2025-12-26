document.addEventListener("DOMContentLoaded", () => {

    const tbody = document.querySelector("#vitalsTable tbody");
    const params = new URLSearchParams(window.location.search);
    const patientId = params.get("patient_id");

    if (!patientId) {
        alert("Patient not selected");
        return;
    }

    fetch(`../api/doctor/get-patient-vitals.php?patient_id=${patientId}`)
        .then(res => res.json())
        .then(vitals => {

            tbody.innerHTML = "";

            if (vitals.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5">No vitals recorded</td>
                    </tr>
                `;
                return;
            }

            vitals.forEach(v => {
                tbody.innerHTML += `
                    <tr>
                        <td>${v.recorded_at}</td>
                        <td>${v.heart_rate ?? "-"}</td>
                        <td>${v.systolic ?? "-"} / ${v.diastolic ?? "-"}</td>
                        <td>${v.temperature ?? "-"}</td>
                        <td>${v.glucose ?? "-"}</td>
                    </tr>
                `;
            });
        })
        .catch(() => {
            alert("Failed to load vitals");
        });

});
