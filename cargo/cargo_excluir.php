<?php
require_once './config/conexao.php';
$registro_id = $_GET['registro'] ?? '';

if ($registro_id) {
    try {
        $stmt = $conn->prepare("DELETE FROM cargos WHERE id = ?");
        $stmt->bind_param("i", $registro_id);
        $stmt->execute();
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        echo "<script>alert('Não é possível excluir um cargo que possui funcionários vinculados.'); window.location.href='index.php?id=cargos';</script>";
        exit;
    }
}

header("Location: index.php?id=cargos");
exit;