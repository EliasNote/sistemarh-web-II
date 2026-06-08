<?php
require_once 'conexao.php';
$registro_id = $_GET['registro'] ?? '';

if ($registro_id) {
    try {
        $stmt = $conn->prepare("DELETE FROM cargos WHERE id = ?");
        $stmt->bind_param("i", $registro_id);
        $stmt->execute();
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        // Intercepta o erro caso o cargo não possa ser excluído por ter funcionários atrelados a ele (FOREIGN KEY RESTRICT)
        echo "<script>alert('Não é possível excluir um cargo que possui funcionários vinculados.'); window.location.href='index.php?id=cargos';</script>";
        exit;
    }
}

header("Location: index.php?id=cargos");
exit;