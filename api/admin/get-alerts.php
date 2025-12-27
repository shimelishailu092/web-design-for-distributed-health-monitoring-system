<?php
include("../../database/db_connection.php");

$sql = "
SELECT a.*, u.full_name AS patient
FROM alerts a
JOIN users u ON a.patient_id = u.id
ORDER BY a.created_at DESC
";

$res = $conn->query($sql);
$data = [];

while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
