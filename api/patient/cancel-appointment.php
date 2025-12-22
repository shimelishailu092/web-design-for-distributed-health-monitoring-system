<?php
header('Content-Type: application/json');
require_once "../../database/db_connection.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$appointment_id = $data['appointment_id'];

$stmt = $pdo->prepare("
    UPDATE appointments
    SET status = 'cancelled'
    WHERE id = ? AND patient_id = ?
");
$stmt->execute([$appointment_id, $_SESSION['user_id']]);

echo json_encode(["success" => true, "message" => "Appointment cancelled"]);
