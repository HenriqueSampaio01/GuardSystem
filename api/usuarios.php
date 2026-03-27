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
    $stmt = $pdo->query("SELECT id, nome, cpf FROM usuarios");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($usuarios);
}

function criarUsuario() {
    global $pdo;

    $body = file_get_contents("php://input");
    $dados = json_decode($body, true);

    //Obtendo os dados do frontend
    $nome = trim($dados["nome"]) ?? '';
    $cpf = trim($dados["cpf"]) ?? '';
    $senha = trim($dados["senha"]) ?? '';

    if(!$nome || !$cpf){
        echo json_encode(["mensagem" => "Erro: Nome e CPF são obrigatórios!"]);
        return;
    }
    
    //Criptografando a senha
    $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, cpf, senha) VALUES (:nome, :cpf, :senha)");
    $stmt->execute([":nome" => $nome, ":cpf" => $cpf, ":senha" => $senhaCriptografada]);

    echo json_encode(["mensagem" => "Usuário criado!"]);
}