<?php
require_once "db.php";
require_once "auth.php";
verificarToken();

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

    // Campos obrigatórios
    $natureza    = trim($dados["natureza"] ?? '');
    $local       = trim($dados["local"] ?? '');
    $data        = trim($dados["data"] ?? '');
    $solicitante = trim($dados["solicitante"] ?? '');
    $nome_mae    = trim($dados["nome_mae"] ?? '');
    $historico   = trim($dados["historico"] ?? '');
    $usuario_id  = $dados["usuario_id"] ?? null;
    $assinatura_conselheiro = trim($dados["assinatura_conselheiro"] ?? '');
    $assinatura_responsavel = trim($dados["assinatura_responsavel"] ?? '');
    $assinatura_solicitante = trim($dados["assinatura_solicitante"] ?? '');

    if (!$natureza || !$local || !$data || !$solicitante || !$nome_mae || !$historico || !$usuario_id || !$assinatura_conselheiro || !$assinatura_responsavel || !$assinatura_solicitante) {
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: Preencha todos os campos obrigatórios!"]);
        return;
    }

    // Campos opcionais
    $numero_registro         = $dados["numero_registro"] ?? null;
    $hora                    = trim($dados["hora"] ?? '');
    $rg_mae                  = trim($dados["rg_mae"] ?? '');
    $cpf_mae                 = trim($dados["cpf_mae"] ?? '');
    $profissao_mae           = trim($dados["profissao_mae"] ?? '');
    $nome_pai                = trim($dados["nome_pai"] ?? '');
    $rg_pai                  = trim($dados["rg_pai"] ?? '');
    $cpf_pai                 = trim($dados["cpf_pai"] ?? '');
    $profissao_pai           = trim($dados["profissao_pai"] ?? '');
    $nome_responsavel        = trim($dados["nome_responsavel"] ?? '');
    $rg_responsavel          = trim($dados["rg_responsavel"] ?? '');
    $profissao_responsavel   = trim($dados["profissao_responsavel"] ?? '');
    $endereco_residencial    = trim($dados["endereco_residencial"] ?? '');
    $numero_residencial      = trim($dados["numero_residencial"] ?? '');
    $bairro_residencial      = trim($dados["bairro_residencial"] ?? '');
    $complemento_residencial = trim($dados["complemento_residencial"] ?? '');
    $telefone_residencial    = trim($dados["telefone_residencial"] ?? '');
    $endereco_comercial      = trim($dados["endereco_comercial"] ?? '');
    $numero_comercial        = trim($dados["numero_comercial"] ?? '');
    $bairro_comercial        = trim($dados["bairro_comercial"] ?? '');
    $complemento_comercial   = trim($dados["complemento_comercial"] ?? '');
    $telefone_comercial      = trim($dados["telefone_comercial"] ?? '');

    // Array de adolescentes (opcional — pode não vir no payload)
    $adolescentes = $dados["adolescentes"] ?? [];

    // ===== TRANSAÇÃO =====
    try {
        $pdo->beginTransaction();

        // 1. Inserir a ocorrência
        $stmt = $pdo->prepare("INSERT INTO ocorrencias (
            numero_registro, natureza, local, data, hora, solicitante, historico,
            usuario_id,
            nome_mae, rg_mae, cpf_mae, profissao_mae,
            nome_pai, rg_pai, cpf_pai, profissao_pai,
            nome_responsavel, rg_responsavel, profissao_responsavel,
            endereco_residencial, numero_residencial, bairro_residencial, complemento_residencial, telefone_residencial,
            endereco_comercial, numero_comercial, bairro_comercial, complemento_comercial, telefone_comercial,
            assinatura_conselheiro, assinatura_responsavel, assinatura_solicitante
        ) VALUES (
            :numero_registro, :natureza, :local, :data, :hora, :solicitante, :historico,
            :usuario_id,
            :nome_mae, :rg_mae, :cpf_mae, :profissao_mae,
            :nome_pai, :rg_pai, :cpf_pai, :profissao_pai,
            :nome_responsavel, :rg_responsavel, :profissao_responsavel,
            :endereco_residencial, :numero_residencial, :bairro_residencial, :complemento_residencial, :telefone_residencial,
            :endereco_comercial, :numero_comercial, :bairro_comercial, :complemento_comercial, :telefone_comercial,
            :assinatura_conselheiro, :assinatura_responsavel, :assinatura_solicitante
        )");

        $stmt->execute([
            ":numero_registro"         => $numero_registro,
            ":natureza"                => $natureza,
            ":local"                   => $local,
            ":data"                    => $data,
            ":hora"                    => $hora,
            ":solicitante"             => $solicitante,
            ":historico"               => $historico,
            ":usuario_id"              => $usuario_id,
            ":nome_mae"                => $nome_mae,
            ":rg_mae"                  => $rg_mae,
            ":cpf_mae"                 => $cpf_mae,
            ":profissao_mae"           => $profissao_mae,
            ":nome_pai"                => $nome_pai,
            ":rg_pai"                  => $rg_pai,
            ":cpf_pai"                 => $cpf_pai,
            ":profissao_pai"           => $profissao_pai,
            ":nome_responsavel"        => $nome_responsavel,
            ":rg_responsavel"          => $rg_responsavel,
            ":profissao_responsavel"   => $profissao_responsavel,
            ":endereco_residencial"    => $endereco_residencial,
            ":numero_residencial"      => $numero_residencial,
            ":bairro_residencial"      => $bairro_residencial,
            ":complemento_residencial" => $complemento_residencial,
            ":telefone_residencial"    => $telefone_residencial,
            ":endereco_comercial"      => $endereco_comercial,
            ":numero_comercial"        => $numero_comercial,
            ":bairro_comercial"        => $bairro_comercial,
            ":complemento_comercial"   => $complemento_comercial,
            ":telefone_comercial"      => $telefone_comercial,
            ":assinatura_conselheiro"  => $assinatura_conselheiro,
            ":assinatura_responsavel"  => $assinatura_responsavel,
            ":assinatura_solicitante"  => $assinatura_solicitante,
        ]);

        // Captura o ID da ocorrência recém-inserida
        $ocorrencia_id = $pdo->lastInsertId();

        // 2. Processar cada adolescente do array
        foreach ($adolescentes as $adol) {
            $nome_adol     = trim($adol["nome"] ?? '');
            $data_nasc     = trim($adol["data_nasc"] ?? '');
            $escola        = trim($adol["escola"] ?? '');

            // Ignora entradas sem nome ou data_nasc
            if (!$nome_adol || !$data_nasc) continue;

            // Tenta reutilizar adolescente existente (busca por nome + data_nasc)
            $check = $pdo->prepare("SELECT id FROM adolescente WHERE nome = :nome AND data_nasc = :data_nasc");
            $check->execute([":nome" => $nome_adol, ":data_nasc" => $data_nasc]);
            $existente = $check->fetch(PDO::FETCH_ASSOC);

            if ($existente) {
                // Reutiliza o ID existente
                $adolescente_id = $existente["id"];
            } else {
                // Cria novo registro de adolescente
                $ins = $pdo->prepare("INSERT INTO adolescente (nome, data_nasc, escola) VALUES (:nome, :data_nasc, :escola)");
                $ins->execute([":nome" => $nome_adol, ":data_nasc" => $data_nasc, ":escola" => $escola]);
                $adolescente_id = $pdo->lastInsertId();
            }

            // Vincula adolescente à ocorrência
            $rel = $pdo->prepare("INSERT INTO ocorrencia_adolescente (id_adolescente, id_ocorrencia) VALUES (:id_adolescente, :id_ocorrencia)");
            $rel->execute([":id_adolescente" => $adolescente_id, ":id_ocorrencia" => $ocorrencia_id]);
        }

        $pdo->commit();
        http_response_code(201);
        echo json_encode(["mensagem" => "Ocorrência registrada!"]);

    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        error_log("Erro ao registrar ocorrência: " . $e->getMessage());
        echo json_encode(["mensagem" => "Erro interno ao registrar ocorrência!"]);
    }
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
    $id = $_GET["id"] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: ID é obrigatório!"]);
        return;
    }

    $check = $pdo->prepare("SELECT 1 FROM ocorrencias WHERE id = :id");
    $check->execute([":id" => $id]);
    if (!$check->fetchColumn()) {
        http_response_code(404);
        echo json_encode(["mensagem" => "Erro: Ocorrência não encontrada!"]);
        return;
    }

    try {
        $pdo->beginTransaction();

        // 1. Remove os vínculos com adolescentes primeiro
        $rel = $pdo->prepare("DELETE FROM ocorrencia_adolescente WHERE id_ocorrencia = :id");
        $rel->execute([":id" => $id]);

        // 2. Agora sim deleta a ocorrência
        $stmt = $pdo->prepare("DELETE FROM ocorrencias WHERE id = :id");
        $stmt->execute([":id" => $id]);

        $pdo->commit();
        echo json_encode(["mensagem" => "Ocorrência excluída!"]);

    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        error_log("Erro ao excluir ocorrência: " . $e->getMessage());
        echo json_encode(["mensagem" => "Erro interno ao excluir ocorrência!"]);
    }
}