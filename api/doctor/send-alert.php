<?php
header('Content-Type: application/json');

require_once "../../config/secure_session.php";
require_once "../../database/db.php";

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'doctor'
) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$raw = file_get_contents("php://input");

if (empty($raw)) {
    echo json_encode([
        "success" => false,
        "message" => "Empty request body (frontend not sending JSON)"
    ]);
    exit;
}

$data = json_decode($raw, true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON",
        "raw" => $raw
    ]);
    exit;
}

if (
    empty($data['patient_id']) ||
    empty($data['alert_type']) ||
    empty($data['message'])
) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields",
        "received" => $data
    ]);
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO alerts (patient_id, doctor_id, alert_type, message)
     VALUES (?, ?, ?, ?)"
);

$stmt->execute([
    (int)$data['patient_id'],
    $_SESSION['user_id'],
    $data['alert_type'],
    trim($data['message'])
]);

echo json_encode([
    "success" => true,
    "message" => "Alert sent successfully"
]);
