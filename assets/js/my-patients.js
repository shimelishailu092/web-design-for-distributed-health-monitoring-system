document.addEventListener("DOMContentLoaded", () => {

    const tbody = document.querySelector("#patientsTable tbody");

    fetch("../api/doctor/get-my-patients.php")
        .then(res => res.json())
        .then(patients => {

            tbody.innerHTML = "";

            if (patients.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6">No patients assigned</td>
                    </tr>
                `;
                return;
            }

            patients.forEach((p, i) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${p.full_name}</td>
                        <td>${p.email}</td>
                       <td>${p.dob ?? '-'}</td>
                        <td>${p.phone ?? "-"}</td>
                        <td>
                            <button onclick="viewVitals(${p.id})">
                                View Vitals
                            </button>
                        </td>
                    </tr>
                `;
            });
        });

    window.viewVitals = function (patientId) {
        window.location.href = `patient-vitals.html?patient_id=${patientId}`;
    };

});
