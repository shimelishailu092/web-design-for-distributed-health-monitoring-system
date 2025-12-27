<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.html");
    exit;
}

header("Location: ../../admin/dashboard.html");
exit;
