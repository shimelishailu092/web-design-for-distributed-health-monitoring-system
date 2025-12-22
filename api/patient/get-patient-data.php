<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../../database/db_connection.php";
require_once "../../core/api-response.php";
require_once "../../core/jwt.php";
require_once "../../core/middleware.php"; // for JWT verification


// -----------------------------------------
// VERIFY JWT TOKEN
// -----------------------------------------
$user = authenticateRequest();  
// If invalid, authenticateRequest() already sends 401 response and exits.

$patientId = $user->id;


// -----------------------------------------
// GET PATIENT DATA
// -----------------------------------------
try {
    $query = "SELECT id, name, email, age, gender, phone, address, blood_type, created_at 
              FROM patients 
              WHERE user_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return sendResponse(404, "Patient record not found");
    }

    $patient = $result->fetch_assoc();

    return sendResponse(200, "Patient data loaded successfully", $patient);

} catch (Exception $e) {
    return sendResponse(500, "Server error: " . $e->getMessage());
}

