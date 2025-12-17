<?php
require_once '../../database/db_connection.php';
require_once '../../core/api-response.php';
require_once '../../core/session-manager.php';

checkDoctorLogin();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['alert_id'])) {
    errorResponse("Alert ID required");
}

$alert_id = $data['alert_id'];

$stmt = $conn->prepare(
    "UPDATE alerts SET status='read' WHERE id=?"
);
$stmt->bind_param("i", $alert_id);
$stmt->execute();

successResponse("Alert marked as read");
