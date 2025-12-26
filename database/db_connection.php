<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "health_monitoring";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode([
        "status" => 500,
        "message" => "Database connection failed"
    ]));
}
