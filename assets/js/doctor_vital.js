async function fetchVitals() {
    const tbody = document.getElementById('vitals-body');
    tbody.innerHTML = '<tr><td colspan="6">Loading...</td></tr>';

    try {
        const response = await fetch('../api/doctor/fetch_vitals.php');
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

        const data = await response.json();

        tbody.innerHTML = ''; // Clear previous rows

        if (data.error) {
            tbody.innerHTML = `<tr><td colspan="6">Error: ${data.error}</td></tr>`;
            return;
        }

        if (Array.isArray(data) && data.length > 0) {
            data.forEach(vital => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${vital.patient_id ?? '--'}</td>
                    <td>${vital.heart_rate ?? '--'}</td>
                    <td>${vital.systolic ?? '--'} / ${vital.diastolic ?? '--'}</td>
                    <td>${vital.temperature ?? '--'}</td>
                    <td>${vital.glucose ?? '--'}</td>
                    <td>${vital.recorded_at ?? '--'}</td>
                `;
                tbody.appendChild(row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="6">No vitals found.</td></tr>';
        }

    } catch (error) {
        console.error('Error fetching vitals:', error);
        tbody.innerHTML = `<tr><td colspan="6">Error loading vitals. Please try again later.</td></tr>`;
    }
}

// Initial load
fetchVitals();

// Refresh every 5 seconds
setInterval(fetchVitals, 5000);
