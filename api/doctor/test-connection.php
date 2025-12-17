<?php
header('Content-Type: application/json');

// Test database connection
$host = "localhost";
$db   = "health_monitoring";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$result = [
    "database_connection" => false,
    "table_exists" => false,
    "table_structure" => [],
    "patient_count" => 0,
    "errors" => []
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $result["database_connection"] = true;
    
    // Check if users table exists
    $checkTable = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($checkTable->rowCount() > 0) {
        $result["table_exists"] = true;
        
        // Get table structure
        $columns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_ASSOC);
        $result["table_structure"] = $columns;
        
        // Count patients
        $countStmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'patient'");
        $count = $countStmt->fetch();
        $result["patient_count"] = $count['count'];
    } else {
        $result["errors"][] = "Users table does not exist";
    }
    
} catch (PDOException $e) {
    $result["errors"][] = "Database connection failed: " . $e->getMessage();
    $result["error_code"] = $e->getCode();
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>

