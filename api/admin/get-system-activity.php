<?php
session_start();
include("../../database/db_connection.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

$sql = "
SELECT *
FROM system_logs
ORDER BY timestamp DESC
LIMIT 200
";

$result = $conn->query($sql);

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

echo json_encode($logs);
$conn->close();
