<?php
session_start();
include("../../database/db_connection.php");
include("../../core/logger.php");

// Get JSON data
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    logAction($conn, "Update user failed (invalid input)", "Failed");
    echo json_encode(["message" => "Invalid input"]);
    exit;
}

$id        = intval($data['id']);
$full_name = $data['full_name'];
$email     = $data['email'];
$role      = $data['role'];
$status    = $data['status'];

/* 1️⃣ Get OLD user data (for log comparison) */
$oldStmt = $conn->prepare(
    "SELECT full_name, email, role, status FROM users WHERE id = ?"
);
$oldStmt->bind_param("i", $id);
$oldStmt->execute();
$oldUser = $oldStmt->get_result()->fetch_assoc();

/* 2️⃣ Update user */
$stmt = $conn->prepare(
    "UPDATE users 
     SET full_name=?, email=?, role=?, status=? 
     WHERE id=?"
);

$stmt->bind_param("ssssi", $full_name, $email, $role, $status, $id);

if ($stmt->execute()) {

    /* 3️⃣ Build readable log message */
    $changes = [];

    if ($oldUser['full_name'] !== $full_name)
        $changes[] = "name: '{$oldUser['full_name']}' → '$full_name'";

    if ($oldUser['email'] !== $email)
        $changes[] = "email changed";

    if ($oldUser['role'] !== $role)
        $changes[] = "role: {$oldUser['role']} → $role";

    if ($oldUser['status'] !== $status)
        $changes[] = "status: {$oldUser['status']} → $status";

    $changeText = empty($changes)
        ? "Updated user ID $id (no changes)"
        : "Updated user ID $id (" . implode(", ", $changes) . ")";

    /* 4️⃣ Log SUCCESS */
    logAction($conn, $changeText, "Success");

    echo json_encode(["message" => "User updated successfully"]);

} else {

    /* 5️⃣ Log FAILURE */
    logAction($conn, "Failed updating user ID $id", "Failed");

    echo json_encode(["message" => "Update failed"]);
}
