<?php
include("../../database/db_connection.php");

$result = $conn->query("SELECT id, full_name, email FROM users WHERE role='doctor' ORDER BY full_name ASC");
$doctors = [];
while($row = $result->fetch_assoc()){
    $doctors[] = $row;
}

echo json_encode($doctors);
$conn->close();
