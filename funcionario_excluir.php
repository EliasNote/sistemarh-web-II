<?php
require_once 'conexao.php';
$registro_id = $_GET['registro'] ?? '';

if ($registro_id) {
    $stmt = $conn->prepare("DELETE FROM funcionarios WHERE id = ?");
    $stmt->bind_param("i", $registro_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: index.php?id=funcionarios");
exit;