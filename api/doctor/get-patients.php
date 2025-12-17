<?php
require_once __DIR__ . '/../../database/db_connection.php';
require_once __DIR__ . '/../../core/session-manager.php';
require_once __DIR__ . '/../../core/api-response.php';

checkDoctorLogin();

$doctor_id = $_SESSION['user_id'];

/**
 * If doctor–patient relationship exists, filter by doctor_id
 * Otherwise this fetches all patients
 */
$sql = "
SELECT id, full_name
FROM users
WHERE role = 'patient'
ORDER BY full_name ASC
";

$result = $conn->query($sql);
$patients = $result->fetch_all(MYSQLI_ASSOC);

successResponse($patients);
