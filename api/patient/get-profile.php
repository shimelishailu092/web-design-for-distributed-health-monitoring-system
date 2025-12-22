<?php
header('Content-Type: application/json');
require_once "../../database/db_connection.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT full_name, email, phone
    FROM users
    WHERE id = ?
");
$stmt->execute([$_SESSION['user_id']]);

echo json_encode([
    "success" => true,
    "profile" => $stmt->fetch(PDO::FETCH_ASSOC)
]);
