

// 2️⃣ Function to fetch system metrics from backend API
function fetchSystemStatus() {
  fetch("../api/admin/get-system-status.php")
    .then(res => res.json())
    .then(data => {
      // Update cards
      document.getElementById("uptime").innerText = data.uptime;
      document.getElementById("activeUsers").innerText = data.activeUsers;
      document.getElementById("pendingAlerts").innerText = data.pendingAlerts;

      // Update recent alerts
      const alertsDiv = document.getElementById("alerts");
      alertsDiv.innerHTML = "";

      if (data.recentAlerts.length === 0) {
        alertsDiv.innerHTML = `<div class="alert alert-info">No alerts found.</div>`;
        return;
      }

      data.recentAlerts.forEach(alert => {
        const cls = alert.status === "Success"
          ? "alert-success"
          : "alert-warning";

        alertsDiv.innerHTML += `
          <div class="alert ${cls}">
            <strong>${alert.timestamp} - ${alert.user}:</strong>
            ${alert.action}
          </div>
        `;
      });
    })
    .catch(() => {
      document.getElementById("alerts").innerHTML =
        `<div class="alert alert-warning">Failed to load system status</div>`;
    });
}
