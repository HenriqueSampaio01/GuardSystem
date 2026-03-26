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