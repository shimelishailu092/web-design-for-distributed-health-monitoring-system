<?php
// core/api-response.php

header("Content-Type: application/json");

/**
 * Success JSON response
 */
function successResponse($data = null, $message = "Success") {
    echo json_encode([
        "success" => true,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

/**
 * Error JSON response
 */
function errorResponse($message = "Error", $code = 400) {
    http_response_code($code);
    echo json_encode([
        "success" => false,
        "message" => $message
    ]);
    exit;
}
