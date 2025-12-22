<?php
header('Content-Type: application/json');
require_once "../../database/db_connection.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if ($data['new_password'] !== $data['confirm_password']) {
    echo json_encode(["success" => false, "message" => "Passwords do not match"]);
    exit;
}

$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!password_verify($data['current_password'], $user['password'])) {
    echo json_encode(["success" => false, "message" => "Current password incorrect"]);
    exit;
}

$newHash = password_hash($data['new_password'], PASSWORD_BCRYPT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->execute([$newHash, $_SESSION['user_id']]);

echo json_encode(["success" => true, "message" => "Password changed successfully"]);
