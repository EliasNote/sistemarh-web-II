<?php
$host = 'db';
$user = 'root';
$pass = 'root';
$dbname = 'sistema_rh';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>