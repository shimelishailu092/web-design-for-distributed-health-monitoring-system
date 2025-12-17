<?php
header('Content-Type: application/json');
require_once "../../config/secure_session.php";
require_once "../../database/db.php";

// Check if user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT r.id, r.title, r.file_path, r.created_at,
               CONCAT(u.first_name, ' ', u.last_name) AS patient_name
        FROM reports r
        JOIN users u ON r.patient_id = u.id
        WHERE r.doctor_id = ?
        ORDER BY r.created_at DESC
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "success" => true,
        "reports" => $reports
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage(),
        "reports" => []
    ]);
}
