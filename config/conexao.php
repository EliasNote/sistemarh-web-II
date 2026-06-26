<?php
$host = 'db';
$user = 'root';
$pass = 'root';
$dbname = 'sistema_rh';
$port = 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>