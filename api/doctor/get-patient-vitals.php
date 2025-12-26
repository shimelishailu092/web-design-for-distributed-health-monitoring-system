<?php
session_start();
include("../../database/db_connection.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    http_response_code(403);
    echo json_encode(["success"=>false,"message"=>"Unauthorized"]);
    exit;
}

$doctor_id  = $_SESSION['user_id'];
$patient_id = $_GET['patient_id'] ?? null;

if (!$patient_id) {
    echo json_encode([]);
    exit;
}

// 🔐 Verify patient belongs to this doctor
$check = $conn->prepare("
    SELECT 1 FROM doctor_patient
    WHERE doctor_id = ? AND patient_id = ?
");
$check->bind_param("ii", $doctor_id, $patient_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    http_response_code(403);
    echo json_encode(["success"=>false,"message"=>"Access denied"]);
    exit;
}
$check->close();

// Fetch vitals
$stmt = $conn->prepare("
    SELECT heart_rate, systolic, diastolic, temperature, glucose, recorded_at
    FROM vitals
    WHERE patient_id = ?
    ORDER BY recorded_at DESC
");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$vitals = [];
while ($row = $result->fetch_assoc()) {
    $vitals[] = $row;
}

echo json_encode($vitals);

$stmt->close();
$conn->close();
