<?php
session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([
        "authorized" => false
    ]);
    exit;
}

echo json_encode([
    "authorized" => true,
    "user_name" => $_SESSION['user_name']
]);
