<?php
require_once "db.php";
require_once "auth.php";
verificarToken();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$metodo = $_SERVER["REQUEST_METHOD"];

switch ($metodo) {
    case "GET": listarAdolescentes(); break;
    default:
        http_response_code(405);
        echo json_encode(["mensagem" => "Método não permitido."]);
}

function listarAdolescentes() {
    global $pdo;

    $ocorrencia_id = $_GET["ocorrencia_id"] ?? null;

    if (!$ocorrencia_id) {
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: ocorrencia_id é obrigatório!"]);
        return;
    }

    $stmt = $pdo->prepare("
        SELECT a.id, a.nome, a.data_nasc, a.escola
        FROM adolescente a
        INNER JOIN ocorrencia_adolescente oa ON oa.id_adolescente = a.id
        WHERE oa.id_ocorrencia = :ocorrencia_id
        ORDER BY a.nome
    ");
    $stmt->execute([":ocorrencia_id" => $ocorrencia_id]);
    $adolescentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($adolescentes);
}