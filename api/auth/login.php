<?php
session_start();
include "../../database/db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email    = $_POST['email'];
    $password = $_POST['password'];

    // 1️⃣ Check email
    $stmt = $conn->prepare("SELECT id, full_name, password, role
                           FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // 2️⃣ If email found
    if ($stmt->num_rows > 0) {

        $stmt->bind_result($id, $full_name, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {

            // session
            $_SESSION['user_id']    = $id;
            $_SESSION['user_name']  = $full_name;
            $_SESSION['role']       = $role;

            // redirect by role
            if($role == 'patient'){
                header("Location: ../../patient/dashboard.html");
            } elseif($role == 'doctor'){
                header("Location: ../../doctor/dashboard.html");
            } elseif($role == 'admin'){
                header("Location: ../../admin/dashboard.html");
            }
            exit();

        } else {
            echo "<script>alert('Incorrect password'); 
            window.location.href='login.html';</script>";
            exit();
        }

    } else {
        echo "<script>alert('Account does not exist! Register first.');
        window.location.href='../../patient/register.html';</script>";
        exit();
    }
}
?>
