<?php
header('Content-Type: application/json');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$result = [
    "session_status" => "unknown",
    "user_logged_in" => false,
    "user_role" => null,
    "user_id" => null,
    "database_connected" => false,
    "reports_table_exists" => false,
    "table_structure" => [],
    "errors" => []
];

// Check session
$result["session_status"] = session_status() === PHP_SESSION_ACTIVE ? "active" : "inactive";
$result["user_logged_in"] = isset($_SESSION['user_id']);
$result["user_id"] = $_SESSION['user_id'] ?? null;
$result["user_role"] = $_SESSION['role'] ?? null;

// Check database
try {
    require_once "../../database/db.php";
    $result["database_connected"] = isset($pdo);
    
    if (isset($pdo)) {
        // Check if reports table exists
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'reports'");
        $result["reports_table_exists"] = $tableCheck->rowCount() > 0;
        
        if ($result["reports_table_exists"]) {
            // Get table structure
            $columns = $pdo->query("SHOW COLUMNS FROM reports")->fetchAll(PDO::FETCH_ASSOC);
            $result["table_structure"] = $columns;
            
            // Count reports for this patient
            if ($result["user_logged_in"]) {
                $countStmt = $pdo->prepare("SELECT COUNT(*) as count FROM reports WHERE patient_id = ?");
                $countStmt->execute([$result["user_id"]]);
                $count = $countStmt->fetch();
                $result["patient_reports_count"] = $count['count'];
            }
        }
    }
} catch (Exception $e) {
    $result["errors"][] = $e->getMessage();
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>

