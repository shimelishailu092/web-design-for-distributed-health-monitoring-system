<?php
session_start();
header("Content-Type: application/json");

require_once "../../database/db_connection.php";

/* ---------- AUTH CHECK ---------- */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access"
    ]);
    exit;
}

$patient_id = $_SESSION['user_id'];

/* ---------- QUERY ---------- */
$sql = "
    SELECT doctor_name, appointment_date, status, note
    FROM appointments
    WHERE patient_id = ?
    ORDER BY appointment_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();

$result = $stmt->get_result();
$appointments = [];

while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode([
    "success" => true,
    "appointments" => $appointments
]);

$stmt->close();
$conn->close();
