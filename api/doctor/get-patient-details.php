<?php
header('Content-Type: application/json');
session_start();

// -------------------------------
// 1. Session check
// -------------------------------
if (!isset($_SESSION['doctor_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized. Please log in."]);
    exit;
}

// -------------------------------
// 2. Get patient ID
// -------------------------------
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing patient ID"]);
    exit;
}

$patientId = (int)$_GET['id'];

// -------------------------------
// 3. Database connection (PDO)
// -------------------------------
try {
    $host = 'localhost';
    $db   = 'health_monitoring';
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
// 4. Fetch patient info
// -------------------------------
try {
    // Patient info
    $stmt = $conn->prepare("SELECT full_name, age, gender, condition, last_update, status FROM users WHERE id = :id AND role='patient'");
    $stmt->execute(['id' => $patientId]);
    $patient = $stmt->fetch();

    if (!$patient) {
        http_response_code(404);
        echo json_encode(["error" => "Patient not found"]);
        exit;
    }

    // Example alert: if status is 'critical'
    $alert = null;
    if ($patient['status'] === 'critical') {
        $alert = "High-risk condition detected!";
    }

    // Dummy health metrics (replace with your real metrics table if exists)
    $metrics = [
        ["time"=>"12:00", "heart_rate"=>"120 bpm", "oxygen"=>"92%", "bp"=>"150/90"],
        ["time"=>"11:30", "heart_rate"=>"110 bpm", "oxygen"=>"94%", "bp"=>"145/88"],
    ];

    echo json_encode([
        "name" => $patient['full_name'],
        "age" => $patient['age'],
        "gender" => $patient['gender'],
        "condition" => $patient['condition'],
        "last_update" => $patient['last_update'],
        "alert" => $alert,
        "metrics" => $metrics
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
