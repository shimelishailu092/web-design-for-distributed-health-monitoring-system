<?php
include("../../database/db_connection.php");

if (!isset($_GET['id'])) {
    echo json_encode(["message" => "User ID required"]);
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode(["message" => "User deleted successfully"]);
