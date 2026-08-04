<?php
require_once "db.php";

//Permite que o front JS acessa a API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

//Faz o php ver o metodo que chegou
$metodo = $_SERVER["REQUEST_METHOD"];

//Toma decisão baseado na requisição
switch ($metodo) {
    case "GET":    listarOcorrencias(); break;
    case "POST":   criarOcorrencia();   break; // usado pelo app
    case "PUT":    editarOcorrencia();  break;
    case "DELETE": excluirOcorrencia(); break;
}

function listarOcorrencias()
{
    global $pdo; //acessa a conexão no db.php

    $id = $_GET["id"] ?? null;

    // Join com usuarios para trazer o nome do conselheiro que registrou
    $sql = "SELECT o.id, o.data, o.hora, o.rua, o.bairro, o.cep, o.tipo,
                   o.forma_de_recebimento, o.usuario_id, u.nome AS conselheiro
            FROM ocorrencias o
            LEFT JOIN usuarios u ON u.id = o.usuario_id";

    if ($id) {
        $stmt = $pdo->prepare("$sql WHERE o.id = :id");
        $stmt->execute([":id" => $id]);
        $ocorrencia = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($ocorrencia);
        return;
    }

    $stmt = $pdo->query("$sql ORDER BY o.data DESC, o.hora DESC");
    $ocorrencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($ocorrencias);
}

function criarOcorrencia() {
    global $pdo;

    $body = file_get_contents("php://input");
    $dados = json_decode($body, true);

    //Obtendo os dados enviados pelo app
    $data = trim($dados["data"] ?? '');
    $hora = trim($dados["hora"] ?? '');
    $rua = trim($dados["rua"] ?? '');
    $bairro = trim($dados["bairro"] ?? '');
    $cep = trim($dados["cep"] ?? '');
    $tipo = trim($dados["tipo"] ?? '');
    $formaRecebimento = trim($dados["forma_de_recebimento"] ?? '');
    $usuarioId = $dados["usuario_id"] ?? null;

    if (!$data || !$hora || !$usuarioId) {
        echo json_encode(["mensagem" => "Erro: Data, hora e usuário são obrigatórios!"]);
        return;
    }

    $stmt = $pdo->prepare("INSERT INTO ocorrencias (data, hora, rua, bairro, cep, tipo, forma_de_recebimento, usuario_id)
                            VALUES (:data, :hora, :rua, :bairro, :cep, :tipo, :forma, :usuario_id)");
    $stmt->execute([
        ":data" => $data,
        ":hora" => $hora,
        ":rua" => $rua,
        ":bairro" => $bairro,
        ":cep" => $cep,
        ":tipo" => $tipo,
        ":forma" => $formaRecebimento,
        ":usuario_id" => $usuarioId,
    ]);

    echo json_encode(["mensagem" => "Ocorrência registrada!"]);
}

function editarOcorrencia() {
    global $pdo;
    $id = $_GET["id"] ?? null;
    $body = file_get_contents("php://input");
    $dados = json_decode($body, true);

    if (!$id) {
        echo json_encode(["mensagem" => "Erro: ID é Obrigatório!"]);
        return;
    }

    // O site edita os dados da ocorrência, mas não reatribui o usuario_id
    // (quem registrou continua sendo o conselheiro do app)
    $data = trim($dados["data"] ?? '');
    $hora = trim($dados["hora"] ?? '');
    $rua = trim($dados["rua"] ?? '');
    $bairro = trim($dados["bairro"] ?? '');
    $cep = trim($dados["cep"] ?? '');
    $tipo = trim($dados["tipo"] ?? '');
    $formaRecebimento = trim($dados["forma_de_recebimento"] ?? '');

    $stmt = $pdo->prepare("UPDATE ocorrencias
                            SET data = :data, hora = :hora, rua = :rua, bairro = :bairro,
                                cep = :cep, tipo = :tipo, forma_de_recebimento = :forma
                            WHERE id = :id");
    $stmt->execute([
        ":data" => $data,
        ":hora" => $hora,
        ":rua" => $rua,
        ":bairro" => $bairro,
        ":cep" => $cep,
        ":tipo" => $tipo,
        ":forma" => $formaRecebimento,
        ":id" => $id,
    ]);

    echo json_encode(["mensagem" => "Ocorrência atualizada!"]);
}

function excluirOcorrencia() {
    global $pdo;
    $id = $_GET["id"] ?? null; //Le o ?id da URL

    if (!$id) {
        echo json_encode(["mensagem" => "Erro: ID é Obrigatório!"]);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM ocorrencias WHERE id = :id");
    $stmt->execute([":id" => $id]);

    echo json_encode(["mensagem" => "Ocorrência excluída!"]);
}