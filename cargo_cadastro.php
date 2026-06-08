<?php
require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $salario_base = $_POST['salario_base'] ?? '';
    $descricao = $_POST['descricao'] ?? '';

    if (!empty($nome) && !empty($salario_base)) {
        $stmt = $conn->prepare("INSERT INTO cargos (nome, salario_base, descricao) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $nome, $salario_base, $descricao);

        if ($stmt->execute()) {
            header("Location: index.php?id=cargos");
            exit;
        } else {
            $erro = "Erro ao cadastrar: " . $conn->error;
        }
        $stmt->close();
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}
?>

<section class="page-header">
    <h2>Novo Cargo</h2>
    <a href="?id=cargos" class="btn-primary" style="background: var(--muted); text-decoration: none;">Voltar</a>
</section>

<section class="table-card">
    <?php if ($erro): ?>
        <div style="background: #fef2f2; color: var(--danger); border: 1px solid #fca5a5; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <form action="?id=cargo_cadastro" method="POST" style="max-width: 500px;">
        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Nome do Cargo</label>
            <input type="text" name="nome" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Salário Base (R$)</label>
            <input type="number" step="0.01" name="salario_base" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Descrição</label>
            <textarea name="descricao" rows="4" style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box; font-family: inherit;"></textarea>
        </div>

        <button type="submit" class="btn-primary">Salvar Cargo</button>
    </form>
</section>