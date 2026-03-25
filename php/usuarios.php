<?php


//Permite que o front JS acessa a API
header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json");

//Faz o php ver o metodo que chegou
$metodo = $_SERVER["REQUEST_METHOD"];

//Toma decisão baseado na requisição
switch ($metodo) {
    case 'GET':
        listarUsuarios();
        break;

    case 'POST':
        criarUsuario();
        break;

    case 'PUT':
        editarUsuario();
        break;

    case 'DELETE':
        excluirUsuario();
        break;
}

function listarUsuarios()
{
    $usuarios = [
        ["id" => 1, "nome" => "Alexandre Rodrigues Filho", "cpf" => "000.111.222-33"],
        ["id" => 2, "nome" => "Antonio Augusto Aguiar", "cpf" => "208.179.098-23"],
        ["id" => 3, "nome" => "Henrique Sampaio Occaso", "cpf" => "444.555.666-77"],
        ["id" => 4, "nome" => "Diego Henrique Ribeiro", "cpf" => "389.190.798-19"],
        ["id" => 5, "nome" => "Eduardo Henrique Domingues", "cpf" => "888.999.111-22"],
    ];

    // Converte o array PHP para JSON e envia
    echo json_encode($usuarios);
}