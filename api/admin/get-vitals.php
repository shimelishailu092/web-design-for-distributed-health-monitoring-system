<?php
include("../../database/db_connection.php");

$sql = "
SELECT v.*, u.full_name AS patient
FROM vitals v
JOIN users u ON v.patient_id = u.id
ORDER BY v.recorded_at DESC
";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
