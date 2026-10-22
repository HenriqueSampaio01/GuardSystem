<?php
require_once "db.php";
require_once __DIR__ . "/../vendor/autoload.php";
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
//Permite que o front JS acessa a API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

//Faz o php ver o metodo que chegou
$metodo = $_SERVER["REQUEST_METHOD"];

switch ($metodo) {
    case "POST":   login();   break;
}

function login() {
    global $pdo;

    $body = file_get_contents("php://input");
    $dados = json_decode($body, true);

    $cpf = trim($dados["cpf"] ?? '');
    $senha = trim($dados["senha"] ?? '');

    if(!$cpf || !$senha){
        http_response_code(400);
        echo json_encode(["mensagem" => "Erro: CPF e senha são obrigatórios!"]);
        return;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE cpf = :cpf");
    $stmt->execute([":cpf" => $cpf]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Mesma resposta para CPF inexistente e senha errada: impede descobrir quais CPFs estão cadastrados
    if(!$usuario || !password_verify($senha, $usuario["senha"])){
        http_response_code(401);
        echo json_encode(["mensagem" => "Erro: CPF ou senha inválidos!"]);
        return;
    }

    $payload = [
        "id"   => $usuario["id"],
        "nome" => $usuario["nome"],
        "iat"  => time(),
        "exp"  => time() + 8 * 3600 // token expira em 8 horas
    ];
$chaveSecreta = getenv('JWT_SECRET');
$token = JWT::encode($payload, $chaveSecreta, 'HS256');
echo json_encode(["mensagem" => "Login realizado com sucesso!", "usuario" => ["nome" => $usuario["nome"], "id" => $usuario["id"]], "token" => $token]);

}