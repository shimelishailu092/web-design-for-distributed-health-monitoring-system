<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once "../../config/secure_session.php";
require_once "../../database/db.php";

// Ensure the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access"
    ]);
    exit;
}

$patient_id = $_SESSION['user_id'];

// Function to fetch single row safely
function fetchSingle($pdo, $query, $id) {
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}

try {
    // 1. Fetch latest vitals ---------------------------------------------
    $vitals = fetchSingle($pdo, "
        SELECT heart_rate, systolic, diastolic, temperature, glucose, recorded_at
        FROM vitals
        WHERE patient_id = ?
        ORDER BY recorded_at DESC
        LIMIT 1
    ", $patient_id);

    // 2. Fetch alerts ------------------------------------------------------
    $alertsQuery = $pdo->prepare("
        SELECT id, alert_type, message, created_at, status
        FROM alerts
        WHERE patient_id = ?
        ORDER BY created_at DESC
        LIMIT 10
    ");
    $alertsQuery->execute([$patient_id]);
    $alerts = $alertsQuery->fetchAll(PDO::FETCH_ASSOC);

    // 3. Medication reminders ----------------------------------------------
    $medsQuery = $pdo->prepare("
        SELECT id, medication_name, dosage, schedule_time, status
        FROM medications
        WHERE patient_id = ?
        ORDER BY schedule_time ASC
    ");
    $medsQuery->execute([$patient_id]);
    $medications = $medsQuery->fetchAll(PDO::FETCH_ASSOC);

    // 4. Appointments -------------------------------------------------------
    $appointmentQuery = $pdo->prepare("
        SELECT id, doctor_name, appointment_date, status, note
        FROM appointments
        WHERE patient_id = ?
        ORDER BY appointment_date ASC
    ");
    $appointmentQuery->execute([$patient_id]);
    $appointments = $appointmentQuery->fetchAll(PDO::FETCH_ASSOC);

    // 5. Doctor reports ------------------------------------------------------
    $reportsQuery = $pdo->prepare("
        SELECT r.id, r.title, r.file_path, r.created_at,
               CONCAT(u.first_name, ' ', u.last_name) AS doctor_name
        FROM reports r
        JOIN users u ON r.doctor_id = u.id
        WHERE r.patient_id = ?
        ORDER BY r.created_at DESC
    ");
    $reportsQuery->execute([$patient_id]);
    $reports = $reportsQuery->fetchAll(PDO::FETCH_ASSOC);

    // 6. Patient profile -----------------------------------------------------
    $profile = fetchSingle($pdo, "
        SELECT id, CONCAT(first_name, ' ', last_name) AS full_name, email, phone, created_at
        FROM users
        WHERE id = ?
    ", $patient_id);

    // Final JSON Response ----------------------------------------------------
    echo json_encode([
        "success" => true,
        "data" => [
            "vitals" => $vitals ?: null,
            "alerts" => $alerts ?: [],
            "medications" => $medications ?: [],
            "appointments" => $appointments ?: [],
            "reports" => $reports ?: [],
            "profile" => $profile ?: null
        ]
    ], JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}

exit;
?>
