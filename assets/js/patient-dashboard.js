document.addEventListener("DOMContentLoaded", () => {
    const apiUrl = "../../api/patient/dashboard.php";

    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert(data.message);
                return;
            }

            const dashboardData = data.data;

            // Profile info
            document.getElementById("user-name").innerText = dashboardData.profile.full_name;
            document.getElementById("user-id").innerText = "Patient ID: " + dashboardData.profile.id;
            document.getElementById("user-avatar").innerText = dashboardData.profile.full_name.split(' ').map(n => n[0]).join('');

            // Current Date
            const dateEl = document.getElementById("current-date");
            const now = new Date();
            dateEl.innerText = now.toLocaleDateString() + " " + now.toLocaleTimeString();

            // Vitals
            if (dashboardData.vitals) {
                document.getElementById("hr-value").innerText = dashboardData.vitals.heart_rate + " bpm";
                document.getElementById("bp-value").innerText = dashboardData.vitals.blood_pressure;
                document.getElementById("temp-value").innerText = dashboardData.vitals.temperature + " °F";
                document.getElementById("glu-value").innerText = dashboardData.vitals.glucose + " mg/dL";

                // Optional: color coding based on normal ranges
                function setStatus(id, value, normalRange) {
                    const el = document.getElementById(id);
                    el.className = value >= normalRange.min && value <= normalRange.max ? "status-normal" : "status-alert";
                    el.innerText = el.className === "status-normal" ? "Normal" : "Alert!";
                }

                setStatus("hr-status", parseInt(dashboardData.vitals.heart_rate), {min: 60, max: 100});
                setStatus("bp-status", parseInt(dashboardData.vitals.blood_pressure.split("/")[0]), {min: 90, max: 120});
                setStatus("temp-status", parseFloat(dashboardData.vitals.temperature), {min: 97, max: 99});
                setStatus("glu-status", parseInt(dashboardData.vitals.glucose), {min: 70, max: 140});

                document.getElementById("hr-last").innerText = "Last updated: " + dashboardData.vitals.recorded_at;
                document.getElementById("bp-last").innerText = "Last updated: " + dashboardData.vitals.recorded_at;
                document.getElementById("temp-last").innerText = "Last updated: " + dashboardData.vitals.recorded_at;
                document.getElementById("glu-last").innerText = "Last updated: " + dashboardData.vitals.recorded_at;
            }

            // Alerts
            const alertsList = document.getElementById("alerts-list");
            alertsList.innerHTML = "";
            dashboardData.alerts.forEach(alert => {
                const div = document.createElement("div");
                div.className = alert.status === "unread" ? "alert-item new-alert" : "alert-item";
                div.innerHTML = `<strong>${alert.alert_type}</strong>: ${alert.message} <span class="time">${alert.created_at}</span>`;
                alertsList.appendChild(div);
            });
            document.getElementById("alerts-count").innerText = dashboardData.alerts.filter(a => a.status === "unread").length + " New";

            // Medications
            const medList = document.getElementById("med-list");
            medList.innerHTML = "";
            dashboardData.medications.forEach(med => {
                const li = document.createElement("li");
                li.innerText = `${med.medication_name} - ${med.dosage} at ${med.schedule_time} [${med.status}]`;
                medList.appendChild(li);
            });

            document.getElementById("mark-all").addEventListener("click", () => {
                fetch("../../api/patient/mark-medications.php", { method: "POST", credentials: "include" })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.success) alert("All medications marked as taken!");
                        location.reload();
                    });
            });

            // Appointments
            const appointmentList = document.getElementById("appointment-list");
            appointmentList.innerHTML = "";
            dashboardData.appointments.forEach(app => {
                const div = document.createElement("div");
                div.className = "appointment-item";
                div.innerHTML = `${app.appointment_date} with Dr. ${app.doctor_name} [${app.status}]`;
                appointmentList.appendChild(div);
            });

            // Reports
            const reportList = document.getElementById("report-list");
            reportList.innerHTML = "";
            dashboardData.reports.forEach(rep => {
                const div = document.createElement("div");
                div.className = "report-item";
                div.innerHTML = `<a href="../../${rep.file_path}" target="_blank">${rep.title}</a> from Dr. ${rep.doctor_name} (${rep.created_at})`;
                reportList.appendChild(div);
            });
        })
        .catch(err => {
            console.error("Error fetching dashboard data:", err);
        });
});
