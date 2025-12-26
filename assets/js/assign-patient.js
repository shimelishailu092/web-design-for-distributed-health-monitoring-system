document.addEventListener("DOMContentLoaded", () => {

    const doctorSelect = document.getElementById("doctorSelect");
    const patientsList = document.getElementById("patientsList");
    const assignBtn = document.getElementById("assignBtn");

    // ==========================
    // LOAD DOCTORS
    // ==========================
    fetch("../api/admin/get-doctors.php")
        .then(res => res.json())
        .then(doctors => {
            doctors.forEach(d => {
                const opt = document.createElement("option");
                opt.value = d.id;
                opt.textContent = d.full_name;
                doctorSelect.appendChild(opt);
            });
        });

    // ==========================
    // LOAD PATIENTS WHEN DOCTOR CHANGES
    // ==========================
    doctorSelect.addEventListener("change", () => {
        const doctorId = doctorSelect.value;

        if (!doctorId) {
            patientsList.innerHTML = "<p>Select a doctor first.</p>";
            return;
        }

        fetch(`../api/admin/get-patients.php?doctor_id=${doctorId}`)
            .then(res => res.json())
            .then(patients => {
                patientsList.innerHTML = "";

                if (patients.length === 0) {
                    patientsList.innerHTML = "<p>No patients found.</p>";
                    return;
                }

                patients.forEach(p => {
                    patientsList.innerHTML += `
                        <div>
                            <label>
                                <input type="checkbox" value="${p.id}" ${p.assigned == 1 ? "checked" : ""}>
                                ${p.full_name} (${p.email})
                            </label>
                        </div>
                    `;
                });
            });
    });

    // ==========================
    // ASSIGN PATIENTS
    // ==========================
    assignBtn.addEventListener("click", () => {

        const doctorId = doctorSelect.value;

        if (!doctorId) {
            alert("Please select a doctor");
            return;
        }

        const checkedBoxes = patientsList.querySelectorAll("input[type='checkbox']:checked");

        if (checkedBoxes.length === 0) {
            alert("Please select at least one patient");
            return;
        }

        // Build FormData correctly
        const formData = new FormData();
        formData.append("doctor_id", doctorId);

        checkedBoxes.forEach(cb => {
            formData.append("patients[]", cb.value);
        });

        fetch("../api/admin/assign-patients.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
        })
        .catch(err => {
            console.error("Assign error:", err);
            alert("Assignment failed");
        });

    });

});
