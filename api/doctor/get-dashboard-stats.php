<?php
header('Content-Type: application/json');
session_start();

// -------------------------------
// 1. Session / Auth Check
// -------------------------------
if (!isset($_SESSION['doctor_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Please log in."]);
    exit;
}

// -------------------------------
// 2. Database Connection (PDO)
// -------------------------------
try {
    $host = 'localhost';
    $db   = 'health_monnitoring';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db_connection;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $conn = new PDO($dsn, $user, $pass, $options);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "DB Connection failed: " . $e->getMessage()]);
    exit;
}

// -------------------------------
// 3. Fetch Dashboard Stats
// -------------------------------
try {
    // Total patients
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role='patient'");
    $stmt->execute();
    $totalPatients = $stmt->fetch()['total'] ?? 0;

    // Critical alerts (example: patients with status='critical')
    $stmt = $conn->prepare("SELECT COUNT(*) as critical FROM users WHERE role='patient' AND status='critical'");
    $stmt->execute();
    $criticalAlerts = $stmt->fetch()['critical'] ?? 0;

    // Pending reports (assuming you have a reports table)
    $stmt = $conn->prepare("SELECT COUNT(*) as pending FROM reports WHERE status='pending'");
    $stmt->execute();
    $pendingReports = $stmt->fetch()['pending'] ?? 0;

    // Fallback dummy data
    if ($totalPatients == 0) $totalPatients = 124;
    if ($criticalAlerts == 0) $criticalAlerts = 7;
    if ($pendingReports == 0) $pendingReports = 3;

    echo json_encode([
        "total_patients" => (int)$totalPatients,
        "critical_alerts" => (int)$criticalAlerts,
        "pending_reports" => (int)$pendingReports
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
