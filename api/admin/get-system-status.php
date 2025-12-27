<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success"=>false,"message"=>"Unauthorized"]);
    exit;
}

$status = [
    "server_time" => date("Y-m-d H:i:s"),
    "php_version" => phpversion(),
    "server_software" => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    "server_status" => "Running"
];

// Uptime (Linux only, safe fallback)
$uptime = @shell_exec("uptime -p");
$status["uptime"] = $uptime ? trim($uptime) : "Not available";

echo json_encode($status);
