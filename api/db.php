<?php

$host = getenv('DB_HOST');
$banco = getenv('DB_NAME');
$porta = getenv('DB_PORT');
$usuario = getenv('DB_USER');
$senha = getenv('DB_PASSWORD');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Responde requisições OPTIONS (preflight) e encerra
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

try {
    $pdo = new PDO("pgsql:host=$host;port=$porta;dbname=$banco", $usuario, $senha);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Detalhes técnicos ficam só no log do servidor, nunca na resposta
    error_log("Falha de conexão com o banco: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["mensagem" => "Erro interno de conexão com o banco de dados."]);
    exit;
}