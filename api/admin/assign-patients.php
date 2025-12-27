<?php
include("../../database/db_connection.php");

$doctor_id = $_POST['doctor_id'] ?? null;
$patients  = $_POST['patients'] ?? [];

if (!$doctor_id || empty($patients)) {
    echo json_encode([
        "success" => false,
        "message" => "Doctor and patients are required"
    ]);
    exit;
}

// Remove existing assignments for this doctor
$stmt = $conn->prepare("DELETE FROM doctor_patient WHERE doctor_id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$stmt->close();

// Insert new assignments
$stmt = $conn->prepare("INSERT INTO doctor_patient (doctor_id, patient_id) VALUES (?, ?)");

foreach ($patients as $patient_id) {
    $stmt->bind_param("ii", $doctor_id, $patient_id);
    $stmt->execute();
}

$stmt->close();

echo json_encode([
    "success" => true,
    "message" => "Patients assigned successfully"
]);

$conn->close();
