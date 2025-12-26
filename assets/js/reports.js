document.addEventListener("DOMContentLoaded", loadReports);

function loadReports() {
    const reportsList = document.getElementById("reports-list");

    fetch("../api/patient/get-reports.php", {
        credentials: "include"
    })
    .then(res => {
        if (!res.ok) {
            throw new Error("Server error: " + res.status);
        }
        return res.json();
    })
    .then(data => {
        if (!data.success) {
            throw new Error(data.message);
        }

        if (data.reports.length === 0) {
            reportsList.innerHTML = "<p>No reports available.</p>";
            return;
        }

        reportsList.innerHTML = "";

        data.reports.forEach(r => {
            reportsList.innerHTML += `
                <div class="report-card">
                    <div>
                        <strong>${r.title}</strong><br>
                        <small>${r.created_at}</small>
                    </div>
                    <a class="download-btn" href="../${r.file}" download>
                        Download
                    </a>
                </div>
            `;
        });
    })
    .catch(err => {
        console.error(err);
        reportsList.innerHTML = `
            <p style="color:red;">Error loading reports.</p>
            <button onclick="location.reload()">Retry</button>
        `;
    });
}
