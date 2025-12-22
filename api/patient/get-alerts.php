<?php
header("Content-Type: application/json");
require_once "../../config/secure_session.php";
require_once "../../database/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access"
    ]);
    exit;
}

$patient_id = (int)$_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT id, alert_type, message, status, created_at
        FROM alerts
        WHERE patient_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$patient_id]);

    echo json_encode([
        "success" => true,
        "alerts" => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Failed to load alerts"
    ]);
}
