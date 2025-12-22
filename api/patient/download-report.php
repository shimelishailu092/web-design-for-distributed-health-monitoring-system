<?php
require_once "../../config/secure_session.php";
require_once "../../database/db.php";

// Check if user is logged in and is a patient
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    http_response_code(403);
    die("Unauthorized access");
}

$report_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$patient_id = $_SESSION['user_id'];

if (!$report_id) {
    http_response_code(400);
    die("Invalid report ID");
}

try {
    $stmt = $pdo->prepare("
        SELECT file_path, title
        FROM reports
        WHERE id = ? AND patient_id = ?
    ");
    
    $stmt->execute([$report_id, $patient_id]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$report) {
        http_response_code(404);
        die("Report not found");
    }
    
    $filePath = "../../" . $report['file_path'];
    
    if (!file_exists($filePath)) {
        http_response_code(404);
        die("File not found on server");
    }
    
    // Sanitize filename for download
    $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $report['title']) . ".pdf";
    
    // Set headers for file download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    // Output file
    readfile($filePath);
    exit;
    
} catch (PDOException $e) {
    http_response_code(500);
    die("Database error: " . $e->getMessage());
}
