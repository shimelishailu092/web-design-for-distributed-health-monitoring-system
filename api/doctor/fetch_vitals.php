<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include '../../database/db_connection.php';

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Fetch latest vitals per patient
$sql = "SELECT v1.*
        FROM vitals v1
        INNER JOIN (
            SELECT patient_id, MAX(recorded_at) AS max_time
            FROM vitals
            GROUP BY patient_id
        ) v2 ON v1.patient_id = v2.patient_id AND v1.recorded_at = v2.max_time
        ORDER BY v1.patient_id ASC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => 'SQL Error: ' . $conn->error]);
    exit;
}

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'No vitals found in the database']);
    exit;
}

$vitals = [];
while($row = $result->fetch_assoc()) {
    $vitals[] = $row;
}

echo json_encode($vitals);
?>
