<?php
require_once './config/conexao.php';
$registro_id = $_GET['registro'] ?? '';

if ($registro_id) {
    $stmt = $conn->prepare("DELETE FROM folha_pagamento WHERE id = ?");
    $stmt->bind_param("i", $registro_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: index.php?id=folha");
exit;