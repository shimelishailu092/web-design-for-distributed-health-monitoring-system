<?php
header("Content-Type: application/json");
require_once "../../config/secure_session.php";
require_once "../../database/db.php";

// ---------------- AUTH ----------------
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'patient') {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);
    exit;
}

// ---------------- METHOD ----------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "POST request required",
        "method" => $_SERVER['REQUEST_METHOD']
    ]);
    exit;
}

// ---------------- INPUT VALIDATION ----------------
$requiredFields = ['heart_rate','systolic','diastolic','temperature','glucose'];
foreach($requiredFields as $field){
    if(!isset($_POST[$field]) || $_POST[$field] === ''){
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Missing field: $field",
            "received" => $_POST
        ]);
        exit;
    }
}

$heart_rate  = (int) $_POST['heart_rate'];
$systolic    = (int) $_POST['systolic'];
$diastolic   = (int) $_POST['diastolic'];
$temperature = (float) $_POST['temperature'];
$glucose     = (int) $_POST['glucose'];

$patient_id = (int) $_SESSION['user_id'];
$doctor_id  = 2;

// ---------------- SAVE VITALS ----------------
try {
    $stmt = $pdo->prepare("INSERT INTO vitals
        (patient_id, heart_rate, systolic, diastolic, temperature, glucose)
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$patient_id,$heart_rate,$systolic,$diastolic,$temperature,$glucose]);
    $vital_id = $pdo->lastInsertId();
} catch(PDOException $e){
    http_response_code(500);
    echo json_encode([
        "success"=>false,
        "message"=>"Database insert failed",
        "error"=>$e->getMessage()
    ]);
    exit;
}

// ---------------- ALERT LOGIC ----------------
$alert_type = "normal";
$alert_msg  = "Vitals normal";

if($heart_rate>120 || $temperature>38.5 || $glucose>180 || $systolic>160 || $diastolic>100){
    $alert_type = "critical";
    $alert_msg = "Critical vitals detected";
} elseif($heart_rate>100 || $temperature>37.5 || $glucose>140){
    $alert_type = "warning";
    $alert_msg = "Abnormal vitals detected";
}

if($alert_type !== "normal"){
    $stmt = $pdo->prepare("INSERT INTO alerts (patient_id, doctor_id, alert_type, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$patient_id,$doctor_id,$alert_type,$alert_msg]);
}

// ---------------- SUCCESS ----------------
echo json_encode([
    "success" => true,
    "message" => "Vitals saved successfully",
    "vital_id" => $vital_id,
    "alert"    => $alert_type
]);
