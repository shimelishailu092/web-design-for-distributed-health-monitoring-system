<?php
include("../../database/db_connection.php");
header("Content-Type: application/json");

// SQL with SAFE joins
$sql = "
SELECT 
    r.id,
    r.title,
    r.file_path,
    r.created_at,
    p.full_name AS patient,
    d.full_name AS doctor
FROM reports r
LEFT JOIN users p ON r.patient_id = p.id
LEFT JOIN users d ON r.doctor_id = d.id
ORDER BY r.created_at DESC
";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "error" => $conn->error
    ]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
