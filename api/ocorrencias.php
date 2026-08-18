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

    if ($id) {
        $stmt = $pdo->prepare("SELECT o.*, u.nome AS conselheiro
                                FROM ocorrencias o
                                LEFT JOIN usuarios u ON u.id = o.usuario_id
                                WHERE o.id = :id");
        $stmt->execute([":id" => $id]);
        $ocorrencia = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ocorrencia) {
            http_response_code(404);
            echo json_encode(["mensagem" => "Erro: Ocorrência não encontrada!"]);
            return;
        }

        echo json_encode($ocorrencia);
        return;
    }

    $stmt = $pdo->query("SELECT o.id, o.data, o.local, u.nome AS conselheiro
                        FROM ocorrencias o
                        LEFT JOIN usuarios u ON u.id = o.usuario_id
                        ORDER BY o.data DESC");
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
        http_response_code(400);
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

    $numero_registro      = $dados["numero_registro"] ?? null;
    $natureza             = trim($dados["natureza"] ?? '');
    $local                = trim($dados["local"] ?? '');
    $data                 = trim($dados["data"] ?? '');
    $hora                 = trim($dados["hora"] ?? '');
    $solicitante          = trim($dados["solicitante"] ?? '');
    $historico            = trim($dados["historico"] ?? '');

    $nome_mae             = trim($dados["nome_mae"] ?? '');
    $rg_mae               = trim($dados["rg_mae"] ?? '');
    $cpf_mae              = trim($dados["cpf_mae"] ?? '');
    $profissao_mae        = trim($dados["profissao_mae"] ?? '');

    $nome_pai             = trim($dados["nome_pai"] ?? '');
    $rg_pai               = trim($dados["rg_pai"] ?? '');
    $cpf_pai              = trim($dados["cpf_pai"] ?? '');
    $profissao_pai        = trim($dados["profissao_pai"] ?? '');

    $nome_responsavel     = trim($dados["nome_responsavel"] ?? '');
    $rg_responsavel       = trim($dados["rg_responsavel"] ?? '');
    $profissao_responsavel = trim($dados["profissao_responsavel"] ?? '');

    $endereco_residencial   = trim($dados["endereco_residencial"] ?? '');
    $numero_residencial     = trim($dados["numero_residencial"] ?? '');
    $bairro_residencial     = trim($dados["bairro_residencial"] ?? '');
    $complemento_residencial = trim($dados["complemento_residencial"] ?? '');
    $telefone_residencial   = trim($dados["telefone_residencial"] ?? '');

    $endereco_comercial     = trim($dados["endereco_comercial"] ?? '');
    $numero_comercial       = trim($dados["numero_comercial"] ?? '');
    $bairro_comercial       = trim($dados["bairro_comercial"] ?? '');
    $complemento_comercial  = trim($dados["complemento_comercial"] ?? '');
    $telefone_comercial     = trim($dados["telefone_comercial"] ?? '');

    $stmt = $pdo->prepare("UPDATE ocorrencias SET
        numero_registro       = :numero_registro,
        natureza              = :natureza,
        local                 = :local,
        data                  = :data,
        hora                  = :hora,
        solicitante           = :solicitante,
        historico             = :historico,
        nome_mae              = :nome_mae,
        rg_mae                = :rg_mae,
        cpf_mae               = :cpf_mae,
        profissao_mae         = :profissao_mae,
        nome_pai              = :nome_pai,
        rg_pai                = :rg_pai,
        cpf_pai               = :cpf_pai,
        profissao_pai         = :profissao_pai,
        nome_responsavel      = :nome_responsavel,
        rg_responsavel        = :rg_responsavel,
        profissao_responsavel = :profissao_responsavel,
        endereco_residencial  = :endereco_residencial,
        numero_residencial    = :numero_residencial,
        bairro_residencial    = :bairro_residencial,
        complemento_residencial = :complemento_residencial,
        telefone_residencial  = :telefone_residencial,
        endereco_comercial    = :endereco_comercial,
        numero_comercial      = :numero_comercial,
        bairro_comercial      = :bairro_comercial,
        complemento_comercial = :complemento_comercial,
        telefone_comercial    = :telefone_comercial
        WHERE id = :id");

    $stmt->execute([
        ":numero_registro"        => $numero_registro,
        ":natureza"               => $natureza,
        ":local"                  => $local,
        ":data"                   => $data,
        ":hora"                   => $hora,
        ":solicitante"            => $solicitante,
        ":historico"              => $historico,
        ":nome_mae"               => $nome_mae,
        ":rg_mae"                 => $rg_mae,
        ":cpf_mae"                => $cpf_mae,
        ":profissao_mae"          => $profissao_mae,
        ":nome_pai"               => $nome_pai,
        ":rg_pai"                 => $rg_pai,
        ":cpf_pai"                => $cpf_pai,
        ":profissao_pai"          => $profissao_pai,
        ":nome_responsavel"       => $nome_responsavel,
        ":rg_responsavel"         => $rg_responsavel,
        ":profissao_responsavel"  => $profissao_responsavel,
        ":endereco_residencial"   => $endereco_residencial,
        ":numero_residencial"     => $numero_residencial,
        ":bairro_residencial"     => $bairro_residencial,
        ":complemento_residencial" => $complemento_residencial,
        ":telefone_residencial"   => $telefone_residencial,
        ":endereco_comercial"     => $endereco_comercial,
        ":numero_comercial"       => $numero_comercial,
        ":bairro_comercial"       => $bairro_comercial,
        ":complemento_comercial"  => $complemento_comercial,
        ":telefone_comercial"     => $telefone_comercial,
        ":id"                     => $id,
    ]);

    echo json_encode(["mensagem" => "Ocorrência atualizada!"]);
}

function excluirOcorrencia() {
    global $pdo;
    $id = $_GET["id"] ?? null; //Le o ?id da URL

    if (!$id) {
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: ID é Obrigatório!"]);
        return;
    }

    $check = $pdo->prepare("SELECT 1 FROM ocorrencias WHERE id = :id");
    $check->execute([":id" => $id]);
    if (!$check->fetchColumn()) {
        http_response_code(404);
        echo json_encode(["mensagem" => "Erro: Ocorrência não encontrada!"]);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM ocorrencias WHERE id = :id");
    $stmt->execute([":id" => $id]);

    echo json_encode(["mensagem" => "Ocorrência excluída!"]);
}