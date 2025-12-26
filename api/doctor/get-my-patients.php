<?php
session_start();
include("../../database/db_connection.php");

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    http_response_code(403);
    echo json_encode(["success"=>false,"message"=>"Unauthorized"]);
    exit;
}

$doctor_id = $_SESSION['user_id'];

$sql = "
SELECT 
    u.id,
    u.full_name,
    u.email,
    u.dob,
    u.phone,
    u.status
FROM doctor_patient dp
JOIN users u ON dp.patient_id = u.id
WHERE dp.doctor_id = ?
ORDER BY u.full_name ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$patients = [];
while ($row = $result->fetch_assoc()) {
    $patients[] = $row;
}

echo json_encode($patients);

$stmt->close();
$conn->close();
