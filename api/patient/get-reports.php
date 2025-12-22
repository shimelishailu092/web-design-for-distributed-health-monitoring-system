<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
header("Content-Type: application/json");

require_once "../../database/db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "User not logged in"
    ]);
    exit;
}

$patient_id = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT 
        title, 
        file_path AS file, 
        created_at 
    FROM reports 
    WHERE patient_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$patient_id]);

echo json_encode([
    "success" => true,
    "reports" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
