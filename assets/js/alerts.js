document.addEventListener("DOMContentLoaded", loadAlerts);

function loadAlerts() {
    const alertsList = document.getElementById("alerts-list");
    alertsList.innerHTML = "Loading alerts...";

    fetch("../api/patient/get-alerts.php", {
        credentials: "include"
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alertsList.innerHTML = `<p style="color:red;">${data.message}</p>`;
            return;
        }

        if (data.alerts.length === 0) {
            alertsList.innerHTML = "<p>No alerts available.</p>";
            return;
        }

        alertsList.innerHTML = "";

        data.alerts.forEach(alert => {
            const card = document.createElement("div");
            card.className = `alert-card ${alert.status === 'unread' ? 'unread' : ''}`;

            card.innerHTML = `
                <strong>${alert.alert_type.toUpperCase()}</strong><br>
                <small>${alert.message}</small><br>
                <small>${alert.created_at}</small><br>
                ${alert.status === 'unread'
                    ? `<button class="alert-btn" onclick="markAsRead(${alert.id})">Mark as Read</button>`
                    : ""}
            `;

            alertsList.appendChild(card);
        });
    })
    .catch(() => {
        alertsList.innerHTML = "<p style='color:red;'>Failed to load alerts.</p>";
    });
}

function markAsRead(alertId) {
    fetch("../api/patient/mark-alert-read.php", {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: alertId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) loadAlerts();
        else alert(data.message);
    });
}

/* MARK ALL AS READ */
function markAllRead() {
    fetch("../api/patient/mark-all-alerts-read.php", {
        method: "POST",
        credentials: "include"
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) loadAlerts();
        else alert(data.message);
    });
}
