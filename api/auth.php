<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function verificarToken() {
    $headers = getallheaders();

    $auth = $headers['Authorization'] ?? '';
    $partes = explode("Bearer ", $auth);
    $token = trim($partes[1] ?? '');

    if (!$token) {
        http_response_code(401);
        echo json_encode(["mensagem" => "Erro: Token não fornecido!"]);
        exit;
    }
        
    $chaveSecreta = getenv("JWT_SECRET");

    if (!$chaveSecreta) {
        http_response_code(500);
        echo json_encode(["mensagem" => "Erro interno de autenticação!"]);
        exit;
    }

    try {
        $decoded = JWT::decode($token, new Key($chaveSecreta, "HS256"));
        return (array) $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["mensagem" => "Erro: Token inválido ou expirado!"]);
        exit;
    }
}