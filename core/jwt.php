<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . "/../../vendor/autoload.php";

$JWT_SECRET = "MY_SUPER_SECRET_KEY_2025"; // change later

function createJWT($payload) {
    global $JWT_SECRET;

    $payload['iat'] = time();
    $payload['exp'] = time() + (60 * 60 * 24); // 24 hours

    return JWT::encode($payload, $JWT_SECRET, 'HS256');
}
