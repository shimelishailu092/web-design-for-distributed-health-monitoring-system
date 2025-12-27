<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../../core/api-response.php";

// -----------------------------------------
// READ INPUT JSON
// -----------------------------------------
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    return sendResponse(400, "Invalid JSON format");
}

$heartRate   = isset($input['heart_rate']) ? floatval($input['heart_rate']) : null;
$bpSystolic  = isset($input['blood_pressure_systolic']) ? floatval($input['blood_pressure_systolic']) : null;
$bpDiastolic = isset($input['blood_pressure_diastolic']) ? floatval($input['blood_pressure_diastolic']) : null;
$temperature = isset($input['temperature']) ? floatval($input['temperature']) : null;
$spo2        = isset($input['spo2']) ? floatval($input['spo2']) : null;

$errors = [];

// -----------------------------------------
// VALIDATE SENSOR RANGES
// -----------------------------------------
if ($heartRate !== null && ($heartRate < 30 || $heartRate > 220)) {
    $errors[] = "Heart rate out of range (30-220 bpm)";
}

if ($bpSystolic !== null && ($bpSystolic < 70 || $bpSystolic > 200)) {
    $errors[] = "Systolic blood pressure out of range (70-200 mmHg)";
}

if ($bpDiastolic !== null && ($bpDiastolic < 40 || $bpDiastolic > 130)) {
    $errors[] = "Diastolic blood pressure out of range (40-130 mmHg)";
}

if ($temperature !== null && ($temperature < 30 || $temperature > 45)) {
    $errors[] = "Temperature out of range (30-45°C)";
}

if ($spo2 !== null && ($spo2 < 50 || $spo2 > 100)) {
    $errors[] = "SpO2 out of range (50-100%)";
}

// -----------------------------------------
// RETURN RESULT
// -----------------------------------------
if (count($errors) > 0) {
    return sendResponse(422, "Validation errors", $errors);
} else {
    return sendResponse(200, "Sensor data is valid");
}
