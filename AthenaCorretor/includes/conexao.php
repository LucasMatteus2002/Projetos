<?php
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable( './');
$dotenv->load();



$host = $_ENV ["DB_HOST"];
$usuario_db = $_ENV ["DB_USER"];
$senha_db = $_ENV ["DB_PASS"];
$banco = $_ENV ["DB_NAME"];

$conn = new mysqli($host, $usuario_db, $senha_db, $banco);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>