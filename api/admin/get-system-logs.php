<?php
session_start();
include("../../database/db_connection.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success"=>false,"message"=>"Unauthorized"]);
    exit;
}

$sql = "
SELECT 
    sl.id,
    sl.action,
    sl.role,
    sl.created_at,
    u.full_name
FROM system_logs sl
LEFT JOIN users u ON sl.user_id = u.id
ORDER BY sl.created_at DESC
LIMIT 100
";

$result = $conn->query($sql);

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

echo json_encode($logs);
$conn->close();
