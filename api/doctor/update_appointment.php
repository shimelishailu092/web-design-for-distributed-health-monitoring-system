<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include '../../database/db_connection.php';

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);
$status = $input['status'] ?? '';

if ($id <= 0 || !in_array($status, ['pending','confirmed','cancelled'])) {
    echo json_encode(['error'=>'Invalid input']);
    exit;
}

$stmt = $conn->prepare("UPDATE appointments SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['error'=>$stmt->error]);
}
?>
