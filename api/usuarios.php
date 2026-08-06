<?php
require_once "db.php";

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

    if(!$nome || !$cpf){
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: Nome e CPF são obrigatórios!"]);
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
    
    $nome = trim($dados["nome"] ?? '');
    $cpf = trim($dados["cpf"] ?? '');
    $senha = trim($dados["senha"] ?? '');

    // Senha em branco mantém a senha atual (não re-hasheia)
    if ($senha !== '') {
        $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, cpf = :cpf, senha = :senha WHERE id = :id");
        $stmt->execute([":nome" => $nome, ":cpf" => $cpf, ":senha" => $senhaCriptografada, ":id" => $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, cpf = :cpf WHERE id = :id");
        $stmt->execute([":nome" => $nome, ":cpf" => $cpf, ":id" => $id]);
    }

    echo json_encode(["mensagem" => "Usuário atualizado!"]);
}