<?php
// core/session-manager.php

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Force login for any API
 */
function requireLogin() {
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Unauthorized access. Please login."
        ]);
        exit;
    }
}

/**
 * Patient-only access
 */
function checkPatientLogin() {
    requireLogin();

    if ($_SESSION['role'] !== 'patient') {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Access denied. Patient only."
        ]);
        exit;
    }
}

/**
 * Doctor-only access
 */
function checkDoctorLogin() {
    requireLogin();

    if ($_SESSION['role'] !== 'doctor') {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Access denied. Doctor only."
        ]);
        exit;
    }
}

/**
 * Admin-only access
 */
function checkAdminLogin() {
    requireLogin();

    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Access denied. Admin only."
        ]);
        exit;
    }
}

/**
 * Logout and destroy session
 */
function logoutUser() {
    session_unset();
    session_destroy();
}
