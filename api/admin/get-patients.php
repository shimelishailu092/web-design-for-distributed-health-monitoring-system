<?php
include("../../database/db_connection.php");

$doctor_id = $_GET['doctor_id'] ?? 0;

// Get patients and mark assigned
$sql = "
    SELECT u.id, u.full_name, u.email,
    (dp.doctor_id IS NOT NULL) AS assigned
    FROM users u
    LEFT JOIN doctor_patient dp 
        ON dp.patient_id = u.id AND dp.doctor_id = ?
    WHERE u.role='patient'
    ORDER BY u.full_name ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$patients = [];
while($row = $result->fetch_assoc()){
    $patients[] = $row;
}

echo json_encode($patients);
$stmt->close();
$conn->close();
