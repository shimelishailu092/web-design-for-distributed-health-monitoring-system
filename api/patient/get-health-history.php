<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once "../../database/db_connection.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$patient_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT heart_rate, blood_pressure, temperature, glucose, recorded_at
        FROM vitals
        WHERE patient_id = ?
        ORDER BY recorded_at DESC
        LIMIT 50
    ");
    $stmt->execute([$patient_id]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "records" => $records]);
} catch(PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
