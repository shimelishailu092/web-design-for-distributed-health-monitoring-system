<?php
header('Content-Type: application/json');
require_once "../../config/secure_session.php";
require_once "../../database/db.php";

// Check if user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

// Validate POST data
if (
    !isset($_POST['patient_id']) ||
    !isset($_POST['title']) ||
    !isset($_FILES['report'])
) {
    echo json_encode(["success" => false, "message" => "Missing form data"]);
    exit;
}

$patient_id = intval($_POST['patient_id']);
$title = trim($_POST['title']);

if (empty($title)) {
    echo json_encode(["success" => false, "message" => "Report title is required"]);
    exit;
}

if ($_FILES['report']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["success" => false, "message" => "File upload error: " . $_FILES['report']['error']]);
    exit;
}

// Validate file type
$ext = strtolower(pathinfo($_FILES['report']['name'], PATHINFO_EXTENSION));
$allowed = ['pdf'];
if (!in_array($ext, $allowed)) {
    echo json_encode(["success" => false, "message" => "Only PDF files are allowed"]);
    exit;
}

// Validate file size (max 10MB)
$maxSize = 10 * 1024 * 1024; // 10MB
if ($_FILES['report']['size'] > $maxSize) {
    echo json_encode(["success" => false, "message" => "File size exceeds 10MB limit"]);
    exit;
}

// Upload directory
$dir = "../../uploads/reports/";
if (!is_dir($dir)) {
    if (!mkdir($dir, 0755, true)) {
        echo json_encode(["success" => false, "message" => "Failed to create upload directory"]);
        exit;
    }
}

$filename = uniqid("report_") . "_" . time() . ".pdf";
$path = $dir . $filename;

if (!move_uploaded_file($_FILES['report']['tmp_name'], $path)) {
    echo json_encode(["success" => false, "message" => "Failed to save file"]);
    exit;
}

// Insert into DB
try {
    $stmt = $pdo->prepare("
        INSERT INTO reports (patient_id, doctor_id, title, file_path, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    $filePath = "uploads/reports/" . $filename;
    $stmt->execute([
        $patient_id,
        $_SESSION['user_id'],
        $title,
        $filePath
    ]);
    
    echo json_encode([
        "success" => true,
        "message" => "Report uploaded successfully"
    ]);
} catch (PDOException $e) {
    // Delete uploaded file if database insert fails
    if (file_exists($path)) {
        unlink($path);
    }
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
