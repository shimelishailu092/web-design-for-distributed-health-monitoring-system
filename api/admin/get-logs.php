<?php
session_start();
include("../../database/db_connection.php");

header("Content-Type: application/json");

// protect API
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([]);
    exit;
}

$sql = "SELECT timestamp, user, action, status 
        FROM system_logs 
        ORDER BY timestamp DESC";

$result = $conn->query($sql);

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

echo json_encode($logs);
