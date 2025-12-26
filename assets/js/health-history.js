document.addEventListener("DOMContentLoaded", () => {
    const apiUrl = "../../api/patient/get-health-history.php";

    fetch(apiUrl)
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message);
                return;
            }

            const records = data.records;

            // Fill table
            const tbody = document.getElementById("history-body");
            tbody.innerHTML = "";
            records.forEach(r => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${r.recorded_at}</td>
                    <td>${r.heart_rate}</td>
                    <td>${r.blood_pressure}</td>
                    <td>${r.temperature}</td>
                    <td>${r.glucose}</td>
                `;
                tbody.appendChild(tr);
            });

            // Chart data
            const labels = records.map(r => r.recorded_at);
            const heartData = records.map(r => r.heart_rate);
            const bpData = records.map(r => parseInt(r.blood_pressure.split("/")[0])); // systolic only
            const tempData = records.map(r => r.temperature);
            const glucoseData = records.map(r => r.glucose);

            // Create charts
            function createChart(id, label, data, color) {
                new Chart(document.getElementById(id), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: data,
                            borderColor: color,
                            backgroundColor: color+'33',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            createChart("heartChart", "Heart Rate", heartData, "red");
            createChart("bpChart", "Systolic BP", bpData, "blue");
            createChart("tempChart", "Temperature", tempData, "orange");
            createChart("glucoseChart", "Glucose", glucoseData, "green");
        })
        .catch(err => console.error("Error fetching health history:", err));
});
