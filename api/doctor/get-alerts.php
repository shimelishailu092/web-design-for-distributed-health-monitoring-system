<?php
header('Content-Type: application/json');

require_once "../../config/secure_session.php";
require_once "../../database/db.php";

// Doctor auth check
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'doctor'
) {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "alerts" => []
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            a.id,
            a.alert_type,
            a.message,
            a.status,
            a.created_at,
            u.full_name AS patient_name
        FROM alerts a
        INNER JOIN users u 
            ON a.patient_id = u.id
        WHERE a.doctor_id = ?
        ORDER BY a.created_at DESC
    ");

    $stmt->execute([$_SESSION['user_id']]);
    $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "alerts" => $alerts
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(), // keep while debugging
        "alerts" => []
    ]);
}
