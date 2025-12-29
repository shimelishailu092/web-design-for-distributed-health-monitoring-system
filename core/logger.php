<?php
function logActivity($conn, $user_id, $user, $role, $action, $status = 'success') {

    $stmt = $conn->prepare("
        INSERT INTO system_activity_logs
        (user_id, user, role, action, status)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issss",
        $user_id,
        $user,
        $role,
        $action,
        $status
    );

    $stmt->execute();
    $stmt->close();
}
