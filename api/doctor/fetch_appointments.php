<?php
session_start();
header('Content-Type: application/json');

require_once "../../database/db_connection.php";

// Check login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success'=>false, 'error'=>'Unauthorized']);
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

try {
    if ($role === 'doctor') {
        // Doctor sees all appointments
        $sql = "SELECT a.id, u.full_name AS patient_name, a.doctor_name, a.appointment_date, a.status, a.note
                FROM appointments a
                JOIN users u ON a.patient_id = u.id
                ORDER BY a.appointment_date DESC";
        $stmt = $conn->prepare($sql);
    } elseif ($role === 'patient') {
        // Patient sees only their own
        $sql = "SELECT a.id, u.full_name AS patient_name, a.doctor_name, a.appointment_date, a.status, a.note
                FROM appointments a
                JOIN users u ON a.patient_id = u.id
                WHERE a.patient_id = ?
                ORDER BY a.appointment_date DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
    } else {
        echo json_encode(['success'=>false, 'error'=>'Invalid role']);
        exit;
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        // Ensure status is always included
        if (!isset($row['status']) || $row['status'] === null) {
            $row['status'] = 'Scheduled'; // fallback if empty
        }
        $appointments[] = $row;
    }

    echo json_encode(['success'=>true, 'appointments'=>$appointments]);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['success'=>false, 'error'=>$e->getMessage()]);
}
