<?php
include("../../database/db_connection.php");
$res = $conn->query("SELECT * FROM users ORDER BY id ASC");
$data = [];
while ($row = $res->fetch_assoc()) $data[] = $row;
echo json_encode($data);
