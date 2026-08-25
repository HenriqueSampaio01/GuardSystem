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
    case "GET":    listarUsuarios(); break;
    case "POST":   criarUsuario();   break;
    case "PUT":    editarUsuario();  break;
    case "DELETE": excluirUsuario(); break;
}

function listarUsuarios()
{
    global $pdo; //acessa a conexão no db.php

    $id = $_GET["id"] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT id, nome, cpf FROM usuarios WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            http_response_code(404);
            echo json_encode(["mensagem" => "Erro: Usuário não encontrado!"]);
            return;
        }

        echo json_encode($usuario);
        return;
    }
    $stmt = $pdo->query("SELECT id, nome, cpf FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($usuarios);
}

function criarUsuario() {
    global $pdo;

    $body = file_get_contents("php://input");
    $dados = json_decode($body, true);

    //Obtendo os dados do frontend
    $nome = trim($dados["nome"] ?? '');
    $cpf = trim($dados["cpf"] ?? '');
    $senha = trim($dados["senha"] ?? '');

    //campos obrigatórios
    if(empty($nome) || empty($cpf) || empty($senha)) {
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: Preencha todos os campos!"]);
        return;
    }

    //validação do nome e sobrenome
    $partes = array_values(array_filter(explode(" ", $nome)));
    if(count($partes) < 2 || mb_strlen($partes[0]) < 3 || mb_strlen($partes[1]) < 3) {
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: Informe nome e sobrenome, ambos com no minimo 3 letras."]);
        return;
    }

    //validação CPF
    if (strlen($cpf) !== 14) {
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: Preencha o CPF corretamente."]);
        return;
    }

    //validação da senha
    $senhaValida = strlen($senha) >= 8 
        && preg_match('/[A-Z]/', $senha) 
        && preg_match('/[a-z]/', $senha) 
        && preg_match('/[0-9]/', $senha);

    if (!$senhaValida) {
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: A senha deve ter no mínimo 8 caracteres, com letra maiúscula, minúscula e número."]);
        return;
    }
    
    //Criptografando a senha
    $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, cpf, senha) VALUES (:nome, :cpf, :senha)");
    try {
        $stmt->execute([":nome" => $nome, ":cpf" => $cpf, ":senha" => $senhaCriptografada]);
    } catch (PDOException $e) {
        // 23505 = unique_violation (CPF duplicado)
        if ($e->getCode() === "23505") {
            http_response_code(409);
            echo json_encode(["mensagem" => "Erro: CPF já cadastrado!"]);
            return;
        }
        http_response_code(500);
        echo json_encode(["mensagem" => "Erro interno ao criar usuário!"]);
        return;
    }

    echo json_encode(["mensagem" => "Usuário criado!"]);
}

function excluirUsuario() {
    global $pdo;
    $id = $_GET["id"] ?? null; //Le o ?id da URL

    if(!$id){
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: ID é Obrigatório!"]);
        return;
    }

    $check = $pdo->prepare("SELECT 1 FROM usuarios WHERE id = :id");
    $check->execute([":id" => $id]);
    if (!$check->fetchColumn()) {
        http_response_code(404);
        echo json_encode(["mensagem" => "Erro: Usuário não encontrado!"]);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
    $stmt->execute([":id" => $id]);

    echo json_encode(["mensagem" => "Usuário excluído!"]);
}

function editarUsuario() {
    global $pdo;
    $id = $_GET["id"] ?? null;
    $body = file_get_contents("php://input");
    $dados = json_decode($body, true);

    $nome = trim($dados["nome"] ?? '');
    $cpf = trim($dados["cpf"] ?? '');
    $senha = trim($dados["senha"] ?? '');

    if(!$id){
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: ID é Obrigatório!"]);
        return;
    }

    //campos obrigatórios
    if(empty($nome) || empty($cpf)) {
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: Preencha o nome e CPF!"]);
        return;
    }

    //validação de nome e sobrenome
    $partes = array_values(array_filter(explode(" ", $nome)));
    if (count($partes) < 2 || mb_strlen($partes[0]) < 3 || mb_strlen($partes[1]) < 3) {
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: Informe nome e sobrenome, ambos com no mínimo 3 letras."]);
        return;
    }

    //Validação do CPF
    if (strlen($cpf) !== 14) {
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: Preencha o CPF corretamente."]);
        return;
    }

    $check = $pdo->prepare("SELECT 1 FROM usuarios WHERE id = :id");
    $check->execute([":id" => $id]);
    if (!$check->fetchColumn()) {
        http_response_code(404);
        echo json_encode(["mensagem" => "Erro: Usuário não encontrado!"]);
        return;
    }

    //validação da senha, só se ela for enviada
    if (!empty($senha)) {
        $senhaValida = strlen($senha) >= 8 
            && preg_match('/[A-Z]/', $senha) 
            && preg_match('/[a-z]/', $senha) 
            && preg_match('/[0-9]/', $senha);

        if (!$senhaValida) {
            http_response_code(400);
            echo json_encode(["mensagem" => "Erro: A senha deve ter no mínimo 8 caracteres, com letra maiúscula, minúscula e número."]);
            return;
        }
    }

    try {
        if ($senha !== '') {
            $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, cpf = :cpf, senha = :senha WHERE id = :id");
            $stmt->execute([":nome" => $nome, ":cpf" => $cpf, ":senha" => $senhaCriptografada, ":id" => $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, cpf = :cpf WHERE id = :id");
            $stmt->execute([":nome" => $nome, ":cpf" => $cpf, ":id" => $id]);
        }
    } catch (PDOException $e) {
        if ($e->getCode() === "23505") {
            http_response_code(409);
            echo json_encode(["mensagem" => "Erro: CPF já cadastrado por outro usuário!"]);
            return;
        }
        http_response_code(500);
        echo json_encode(["mensagem" => "Erro interno ao atualizar usuário!"]);
        return;
    }

    echo json_encode(["mensagem" => "Usuário atualizado!"]);
}