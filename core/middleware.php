<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once "api-response.php";
require_once "jwt.php";

function authenticateRequest() {
    $headers = apache_request_headers();

    if (!isset($headers['Authorization'])) {
        sendResponse(401, "Authorization header missing");
    }

    $authHeader = trim($headers['Authorization']);

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        sendResponse(401, "Invalid token format. Use: Bearer <token>");
    }

    $jwt = $matches[1];

    try {
        global $JWT_SECRET;
        $decoded = JWT::decode($jwt, new Key($JWT_SECRET, 'HS256'));
        return $decoded;  // Return user data from token

    } catch (Exception $e) {
        sendResponse(401, "Invalid or expired token");
    }
}
