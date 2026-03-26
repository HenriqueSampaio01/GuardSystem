<?php

$host = "db";
$banco = "guardsystem";
$usuario = "root";
$senha = "root";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8", $usuario, $senha);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["erro" => $e->getMessage()]);
    exit;
}