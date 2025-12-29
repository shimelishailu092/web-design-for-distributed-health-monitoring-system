<?php
// Start a secure session
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 0,        // Session cookie lasts until browser closes
        'cookie_httponly' => true,     // Prevent JS access to session cookie
        'cookie_samesite' => 'Strict', // Prevent CSRF
        'use_strict_mode' => true      // Strict session mode
    ]);
}

// Regenerate session ID on each request (optional, can be done on login)
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Check if user is logged in
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        // Not logged in
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(["success" => false, "message" => "User not logged in"]);
        exit;
    }
}

// Optional: Role check helper
function check_role($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header("HTTP/1.1 403 Forbidden");
        echo json_encode(["success" => false, "message" => "Access denied"]);
        exit;
    }
}

// Optional: destroy session safely
function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
?>
