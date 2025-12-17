<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors, but log them

header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode([
        "success" => false, 
        "message" => "Not logged in. Please log in first.",
        "debug" => [
            "session_id" => session_id(),
            "has_user_id" => isset($_SESSION['user_id']),
            "has_role" => isset($_SESSION['role'])
        ]
    ]);
    exit;
}

if ($_SESSION['role'] !== 'doctor') {
    http_response_code(403);
    echo json_encode([
        "success" => false, 
        "message" => "Access denied. Doctor role required.",
        "debug" => [
            "current_role" => $_SESSION['role'] ?? 'not set'
        ]
    ]);
    exit;
}

// Include database connection
$dbPath = __DIR__ . "/../../database/db.php";
if (!file_exists($dbPath)) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database configuration file not found.",
        "debug" => ["path" => $dbPath]
    ]);
    exit;
}

require_once $dbPath;

// Check if database connection is available
if (!isset($pdo)) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed. Please check server configuration."
    ]);
    exit;
}

try {
    // First, check if users table exists and has the right structure
    $checkTable = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($checkTable->rowCount() === 0) {
        throw new Exception("Users table does not exist. Please run database setup.");
    }
    
    // Check table structure
    $columns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    $hasFirstName = in_array('first_name', $columns);
    $hasLastName = in_array('last_name', $columns);
    $hasFullName = in_array('full_name', $columns);
    
    // Build query based on available columns
    if ($hasFirstName && $hasLastName) {
        $query = "
            SELECT id, CONCAT(first_name, ' ', last_name) AS full_name, email
            FROM users
            WHERE role = 'patient'
            ORDER BY first_name ASC, last_name ASC
        ";
    } elseif ($hasFullName) {
        $query = "
            SELECT id, full_name, email
            FROM users
            WHERE role = 'patient'
            ORDER BY full_name ASC
        ";
    } else {
        throw new Exception("Users table structure is incorrect. Missing first_name/last_name or full_name column.");
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "success" => true,
        "patients" => $patients ?: [],
        "count" => count($patients)
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    $errorMsg = $e->getMessage();
    error_log("PDO Error in get-all-patients.php: " . $errorMsg);
    
    // Don't expose full error in production, but provide helpful message
    echo json_encode([
        "success" => false,
        "message" => "Database error occurred. Please check database connection and table structure.",
        "debug" => (ini_get('display_errors') ? ["error" => $errorMsg] : [])
    ]);
} catch (Exception $e) {
    http_response_code(500);
    $errorMsg = $e->getMessage();
    error_log("Error in get-all-patients.php: " . $errorMsg);
    
    echo json_encode([
        "success" => false,
        "message" => $errorMsg
    ]);
}
