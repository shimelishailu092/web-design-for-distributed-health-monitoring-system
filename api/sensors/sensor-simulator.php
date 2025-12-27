<?php
require_once "../../database/db_connection.php";

// Simulated logged-in patient & assigned doctor
$patient_id = 1;
$doctor_id  = 2;

/* ----------- SENSOR MEASUREMENTS ----------- */
$heart_rate = rand(60, 140);
$systolic   = rand(100, 180);
$diastolic  = rand(60, 120);
$temperature = rand(360, 400) / 10;
$glucose    = rand(70, 220);

/* ----------- SAVE VITALS ----------- */
$stmt = $pdo->prepare("
    INSERT INTO vitals
    (patient_id, heart_rate, systolic, diastolic, temperature, glucose)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->execute([
    $patient_id,
    $heart_rate,
    $systolic,
    $diastolic,
    $temperature,
    $glucose
]);

/* ----------- ALERT LOGIC ----------- */
$alert_level = "normal";
$message = "Vitals are within normal range";

if (
    $heart_rate > 120 ||
    $temperature > 38.5 ||
    $glucose > 180 ||
    $systolic > 160 ||
    $diastolic > 100
) {
    $alert_level = "critical";
    $message = "Critical condition detected! Immediate attention required.";
}
elseif (
    $heart_rate > 100 ||
    $temperature > 37.5 ||
    $glucose > 140
) {
    $alert_level = "warning";
    $message = "Abnormal vitals detected. Monitor patient closely.";
}

/* ----------- SAVE ALERT IF NEEDED ----------- */
if ($alert_level !== "normal") {
    $stmt = $pdo->prepare("
        INSERT INTO alerts
        (patient_id, doctor_id, alert_level, message)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $patient_id,
        $doctor_id,
        $alert_level,
        $message
    ]);
}

/* ----------- API RESPONSE ----------- */
echo json_encode([
    "success" => true,
    "vitals" => [
        "heart_rate" => $heart_rate,
        "blood_pressure" => "$systolic/$diastolic",
        "temperature" => $temperature,
        "glucose" => $glucose
    ],
    "alert_level" => $alert_level
]);
