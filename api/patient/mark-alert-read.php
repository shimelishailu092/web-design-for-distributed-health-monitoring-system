<?php
header("Content-Type: application/json");
require_once "../../config/secure_session.php";
require_once "../../database/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['id'])) {
    echo json_encode(["success" => false, "message" => "Alert ID missing"]);
    exit;
}

$alert_id   = (int)$data['id'];
$patient_id = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("
    UPDATE alerts
    SET status = 'read'
    WHERE id = ? AND patient_id = ?
");

$ok = $stmt->execute([$alert_id, $patient_id]);

echo json_encode([
    "success" => $ok,
    "message" => $ok ? "Updated" : "Update failed"
]);
