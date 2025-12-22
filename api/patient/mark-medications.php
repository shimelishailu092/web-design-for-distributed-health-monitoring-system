<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once "../../database/db_connection.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$patient_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("UPDATE medications SET status='taken' WHERE patient_id = ?");
    $stmt->execute([$patient_id]);

    echo json_encode(["success" => true, "message" => "All medications marked as taken"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
