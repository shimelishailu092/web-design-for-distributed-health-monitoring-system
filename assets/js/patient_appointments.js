const appointmentsBody = document.getElementById('appointmentsBody');

/* ---------------- STATUS LABEL HELPER ---------------- */
function getStatusLabel(status) {
    if (!status) status = 'Scheduled';

    let color = '';
    switch(status.toLowerCase()) {
        case 'scheduled':
            color = 'blue';
            break;
        case 'completed':
            color = 'green';
            break;
        case 'cancelled':
            color = 'red';
            break;
        default:
            color = 'gray';
    }

    return `<span style="color:white; background-color:${color}; padding:2px 6px; border-radius:4px; font-weight:bold;">${status}</span>`;
}

/* ---------------- FETCH PATIENT APPOINTMENTS ---------------- */
async function loadAppointments() {
    try {
        // Patient sees only their own appointments
        const res = await fetch('../api/patient/fetch_appointments.php');
        const data = await res.json();

        appointmentsBody.innerHTML = '';

        if (!data.success || !data.appointments || data.appointments.length === 0) {
            appointmentsBody.innerHTML = '<tr><td colspan="4">No appointments found</td></tr>';
            return;
        }

        data.appointments.forEach(a => {
            const tr = document.createElement('tr');

            // Format MySQL DATETIME (yyyy-mm-dd hh:mm:ss) to dd/mm/yyyy hh:mm:ss
            let formattedDate = '--';
            if (a.appointment_date) {
                const dt = new Date(a.appointment_date.replace(/-/g,'/'));
                const day = String(dt.getDate()).padStart(2,'0');
                const month = String(dt.getMonth()+1).padStart(2,'0');
                const year = dt.getFullYear();
                const hours = String(dt.getHours()).padStart(2,'0');
                const minutes = String(dt.getMinutes()).padStart(2,'0');
                const seconds = String(dt.getSeconds()).padStart(2,'0');
                formattedDate = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
            }

            const statusLabel = getStatusLabel(a.status);

            tr.innerHTML = `
                <td>${a.doctor_name || '--'}</td>
                <td>${formattedDate}</td>
                <td>${statusLabel}</td>
                <td>${a.note || '--'}</td>
            `;
            appointmentsBody.appendChild(tr);
        });

    } catch (err) {
        console.error('Error loading appointments:', err);
        appointmentsBody.innerHTML = '<tr><td colspan="4">Server error loading appointments</td></tr>';
    }
}

/* ---------------- INIT ---------------- */
loadAppointments();
