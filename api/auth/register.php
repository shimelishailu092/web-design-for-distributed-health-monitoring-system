<?php
session_start();
include "../../database/db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='../../patient/register.html';</script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.location.href='../../patient/register.html';</script>";
        exit();
    }

    // Insert patient as user with role 'patient'
    $role = 'patient';
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, dob, phone, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $full_name, $email, $dob, $phone, $hashed_password, $role);
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Please login.'); window.location.href='../../patient/login.html';</script>";
    } else {
        echo "<script>alert('Error! Try again later.'); window.location.href='../../patient/register.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
