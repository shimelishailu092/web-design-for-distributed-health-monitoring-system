<?php
header("Content-Type: application/json");
require_once "../../config/secure_session.php";
require_once "../../database/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$patient_id = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("
    UPDATE alerts
    SET status = 'read'
    WHERE patient_id = ?
");

$ok = $stmt->execute([$patient_id]);

echo json_encode([
    "success" => $ok,
    "message" => "All alerts marked as read"
]);
