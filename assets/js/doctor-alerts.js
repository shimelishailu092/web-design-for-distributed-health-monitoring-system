document.addEventListener("DOMContentLoaded", () => {
    const patientSelect = document.getElementById("patient_id");
    const alertForm = document.getElementById("alertForm");
    const statusEl = document.getElementById("status");
    const alertsContainer = document.getElementById("alertsContainer");

    // Fetch patients and populate dropdown
    function loadPatients() {
        fetch("../api/doctor/get-all-patients.php")
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    console.error("Failed to load patients");
                    return;
                }

                if (!patientSelect) return;

                // Reset options
                patientSelect.innerHTML = "<option value=''>-- Select Patient --</option>";

                data.patients.forEach(p => {
                    const option = document.createElement("option");
                    option.value = p.id;
                    option.textContent = p.full_name;
                    patientSelect.appendChild(option);
                });
            })
            .catch(err => console.error("Error fetching patients:", err));
    }

    // Submit alert
    function submitAlert(e) {
        e.preventDefault();
        if (!patientSelect.value) {
            statusEl.textContent = "Please select a patient.";
            return;
        }

        const alertData = {
            patient_id: patientSelect.value,
            alert_type: document.getElementById("alert_type").value,
            message: document.getElementById("message").value
        };

        fetch("../api/doctor/send-alert.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(alertData)
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    statusEl.textContent = "Alert sent successfully!";
                    alertForm.reset();
                    loadAlerts();
                } else {
                    statusEl.textContent = data.message || "Failed to send alert";
                }
            })
            .catch(err => {
                console.error("Error sending alert:", err);
                statusEl.textContent = "Error sending alert";
            });
    }

    // Load all alerts
    function loadAlerts() {
        fetch("../api/doctor/get-alerts.php")
            .then(res => res.json())
            .then(data => {
                if (!alertsContainer) return;

                if (!data.success || !data.alerts) {
                    alertsContainer.innerHTML = "<p>No alerts available</p>";
                    return;
                }

                if (data.alerts.length === 0) {
                    alertsContainer.innerHTML = "<p>No alerts</p>";
                    return;
                }

                alertsContainer.innerHTML = "";

                data.alerts.forEach(alert => {
                    const alertDiv = document.createElement("div");
                    alertDiv.className = `alert ${alert.alert_type}`;
                    alertDiv.innerHTML = `
                        <strong>${alert.patient_name}</strong><br>
                        ${alert.message}<br>
                        <small>${alert.created_at}</small><br>
                        <button onclick="markRead(${alert.id})">Mark as Read</button>
                    `;
                    alertsContainer.appendChild(alertDiv);
                });
            })
            .catch(err => console.error("Error loading alerts:", err));
    }

    // Initialize
    loadPatients();
    loadAlerts();

    if (alertForm) {
        alertForm.addEventListener("submit", submitAlert);
    }
});

// Mark alert as read
function markRead(id) {
    fetch("../api/doctor/mark-alert-read.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ alert_id: id })
    })
        .then(res => res.json())
        .then(() => {
            // Reload alerts after marking read
            const event = new Event("DOMContentLoaded");
            document.dispatchEvent(event);
        })
        .catch(err => console.error("Error marking alert read:", err));
}
