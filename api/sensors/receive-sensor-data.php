<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../../database/db_connection.php";
require_once "../../core/api-response.php";

// Optional: Secure the sensor with a token
$headers = getallheaders();
$sensorToken = $headers['Sensor-Token'] ?? '';
if ($sensorToken !== 'YOUR_SENSOR_SECRET') {
    return sendResponse(403, "Access denied. Invalid sensor token.");
}

// -----------------------------------------
// READ INPUT JSON
// -----------------------------------------
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    return sendResponse(400, "Invalid JSON format");
}

// Required fields
$patientId       = isset($input['patient_id']) ? intval($input['patient_id']) : 0;
$heartRate       = isset($input['heart_rate']) ? floatval($input['heart_rate']) : null;
$bpSystolic      = isset($input['blood_pressure_systolic']) ? floatval($input['blood_pressure_systolic']) : null;
$bpDiastolic     = isset($input['blood_pressure_diastolic']) ? floatval($input['blood_pressure_diastolic']) : null;
$temperature     = isset($input['temperature']) ? floatval($input['temperature']) : null;
$spo2            = isset($input['spo2']) ? floatval($input['spo2']) : null;

// Validate patient_id
if ($patientId <= 0) {
    return sendResponse(422, "Invalid patient ID");
}

// -----------------------------------------
// INSERT SENSOR DATA
// -----------------------------------------
try {
    $stmt = $conn->prepare("INSERT INTO health_data 
        (user_id, heart_rate, blood_pressure_systolic, blood_pressure_diastolic, temperature, spo2, recorded_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())");

    $stmt->bind_param(
        "iddddd",
        $patientId,
        $heartRate,
        $bpSystolic,
        $bpDiastolic,
        $temperature,
        $spo2
    );

    if ($stmt->execute()) {
        return sendResponse(201, "Sensor data received successfully", [
            "record_id" => $stmt->insert_id
        ]);
    } else {
        return sendResponse(500, "Failed to insert sensor data");
    }

} catch (Exception $e) {
    return sendResponse(500, "Server error: " . $e->getMessage());
}
