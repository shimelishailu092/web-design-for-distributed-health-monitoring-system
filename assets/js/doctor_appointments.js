const patientSelect = document.getElementById('patientSelect');
const appointmentForm = document.getElementById('appointmentForm');
const msg = document.getElementById('msg');
const appointmentsBody = document.getElementById('appointmentsBody');

/* ---------------- FETCH PATIENTS ---------------- */
async function loadPatients() {
    try {
        const res = await fetch('../api/doctor/get-all-patients.php');
        const data = await res.json();

        patientSelect.innerHTML = '<option value="">Select Patient</option>';

        if (data.success && data.patients.length) {
            data.patients.forEach(p => {
                const name = p.full_name || p.name || `Patient ${p.id}`;
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = `${name} (ID: ${p.id})`;
                patientSelect.appendChild(opt);
            });
        } else {
            patientSelect.innerHTML = '<option value="">No patients found</option>';
        }
    } catch (err) {
        patientSelect.innerHTML = '<option value="">Error loading patients</option>';
        console.error('Error loading patients:', err);
    }
}

/* ---------------- FETCH EXISTING APPOINTMENTS ---------------- */
async function loadAppointments() {
    try {
        const res = await fetch('../api/doctor/fetch_appointments.php');
        const data = await res.json();

        appointmentsBody.innerHTML = '';

        if (!data.success || !data.appointments || data.appointments.length === 0) {
            appointmentsBody.innerHTML = '<tr><td colspan="5">No appointments found</td></tr>';
            return;
        }

        data.appointments.forEach(a => {
            const tr = document.createElement('tr');

            // Format MySQL DATETIME (yyyy-mm-dd hh:mm:ss) to dd/mm/yyyy hh:mm:ss
            let formattedDate = '--';
            if (a.appointment_date) {
                const dt = new Date(a.appointment_date.replace(/-/g, '/')); // cross-browser fix
                const day = String(dt.getDate()).padStart(2,'0');
                const month = String(dt.getMonth()+1).padStart(2,'0');
                const year = dt.getFullYear();
                const hours = String(dt.getHours()).padStart(2,'0');
                const minutes = String(dt.getMinutes()).padStart(2,'0');
                const seconds = String(dt.getSeconds()).padStart(2,'0');
                formattedDate = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
            }

            tr.innerHTML = `
                <td>${a.patient_name || '--'}</td>
                <td>${a.doctor_name || '--'}</td>
                <td>${formattedDate}</td>
                <td>${a.status || '--'}</td>
                <td>${a.note || '--'}</td>
            `;
            appointmentsBody.appendChild(tr);
        });

    } catch (err) {
        console.error('Error loading appointments:', err);
        appointmentsBody.innerHTML = '<tr><td colspan="5">Server error loading appointments</td></tr>';
    }
}

/* ---------------- SUBMIT NEW APPOINTMENT ---------------- */
appointmentForm.addEventListener('submit', async e => {
    e.preventDefault();

    const formData = new FormData(appointmentForm);

    const patient_id = formData.get('patient_id');
    const doctor_name = formData.get('doctor_name');
    const note = formData.get('note') || '';
    const appointmentDateInput = formData.get('appointment_date');

    if (!patient_id || !doctor_name || !appointmentDateInput) {
        msg.style.color = 'red';
        msg.innerText = 'Please fill all required fields';
        return;
    }

    // Convert datetime-local (yyyy-mm-ddThh:mm) to MySQL DATETIME (yyyy-mm-dd hh:mm:ss)
    let dt = new Date(appointmentDateInput);
    const yyyy = dt.getFullYear();
    const mm = String(dt.getMonth()+1).padStart(2,'0');
    const dd = String(dt.getDate()).padStart(2,'0');
    const hh = String(dt.getHours()).padStart(2,'0');
    const min = String(dt.getMinutes()).padStart(2,'0');
    const ss = String(dt.getSeconds()).padStart(2,'0');
    const mysqlDatetime = `${yyyy}-${mm}-${dd} ${hh}:${min}:${ss}`;

    const payload = {
        patient_id,
        doctor_name,
        appointment_date: mysqlDatetime,
        note
    };

    try {
        const res = await fetch('../api/doctor/add_appointment.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify(payload)
        });

        const result = await res.json();

        if (result.success) {
            msg.style.color = 'green';
            msg.innerText = 'Appointment created successfully!';
            appointmentForm.reset();
            loadAppointments();
        } else {
            msg.style.color = 'red';
            msg.innerText = result.error || 'Error creating appointment';
        }

    } catch (err) {
        console.error('Server error:', err);
        msg.style.color = 'red';
        msg.innerText = 'Server error';
    }
});

/* ---------------- INIT ---------------- */
loadPatients();
loadAppointments();
