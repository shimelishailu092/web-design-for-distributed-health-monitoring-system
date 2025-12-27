<?php
include("../../database/db_connection.php");

// Check required fields
$required = ['full_name', 'email', 'password', 'role', 'dob', 'phone', 'status'];

foreach ($required as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "$field is required"
        ]);
        exit;
    }
}

// Sanitize inputs
$full_name = trim($_POST['full_name']);
$email     = trim($_POST['email']);
$password  = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role      = $_POST['role'];
$dob       = $_POST['dob'];
$phone     = trim($_POST['phone']);
$status    = $_POST['status'];

$stmt = $conn->prepare("
    INSERT INTO users (full_name, email, password, role, dob, phone, status)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssss",
    $full_name,
    $email,
    $password,
    $role,
    $dob,
    $phone,
    $status
);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "User added successfully"
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
