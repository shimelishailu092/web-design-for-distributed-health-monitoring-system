<?php
session_start();
header('Content-Type: application/json');

require_once "../../database/db_connection.php";

// Check if logged in as doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    echo json_encode(['success'=>false, 'error'=>'Unauthorized. Doctor login required.']);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

$patient_id = $input['patient_id'] ?? null;
$doctor_name = $input['doctor_name'] ?? null;
$appointment_date = $input['appointment_date'] ?? null;
$note = $input['note'] ?? '';

if (!$patient_id || !$doctor_name || !$appointment_date) {
    echo json_encode(['success'=>false, 'error'=>'Patient, doctor, and appointment date are required']);
    exit;
}

try {
    // Prepare SQL with status default to 'Scheduled'
    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_name, appointment_date, status, note, created_at) VALUES (?, ?, ?, 'Scheduled', ?, NOW())");
    $stmt->bind_param("isss", $patient_id, $doctor_name, $appointment_date, $note);

    if ($stmt->execute()) {
        echo json_encode(['success'=>true, 'message'=>'Appointment created successfully']);
    } else {
        echo json_encode(['success'=>false, 'error'=>'Database error: '.$stmt->error]);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
