<?php
header('Content-Type: application/json');
require_once "../../database/db_connection.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare("
    UPDATE users
    SET full_name = ?, email = ?, phone = ?
    WHERE id = ?
");
$stmt->execute([
    $data['full_name'],
    $data['email'],
    $data['phone'],
    $_SESSION['user_id']
]);

echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
