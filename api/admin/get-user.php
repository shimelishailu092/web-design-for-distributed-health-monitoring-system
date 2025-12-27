<?php
include("../../database/db_connection.php");
$id = $_GET['id'];
$res = $conn->query("SELECT * FROM users WHERE id=$id");
echo json_encode($res->fetch_assoc());
