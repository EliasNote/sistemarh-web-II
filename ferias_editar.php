<?php
require_once 'conexao.php';
$erro = '';
$registro_id = $_GET['registro'] ?? '';

// Busca todos os funcionários ativos para popular a seleção
$resultado_func = $conn->query("SELECT id, nome FROM funcionarios WHERE status != 'Inativo' ORDER BY nome ASC");
$funcionarios = $resultado_func ? $resultado_func->fetch_all(MYSQLI_ASSOC) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $funcionario_id = $_POST['funcionario_id'] ?? '';
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';
    $status = $_POST['status'] ?? 'Agendada';

    if (!empty($funcionario_id) && !empty($data_inicio) && !empty($data_fim)) {
        if (strtotime($data_inicio) > strtotime($data_fim)) {
            $erro = "A data de início não pode ser maior do que a data de término das férias.";
        } else {
            $stmt = $conn->prepare("UPDATE ferias SET funcionario_id=?, data_inicio=?, data_fim=?, status=? WHERE id=?");
            $stmt->bind_param("isssi", $funcionario_id, $data_inicio, $data_fim, $status, $registro_id);

            if ($stmt->execute()) {
                header("Location: index.php?id=ferias");
                exit;
            } else {
                $erro = "Erro ao atualizar agendamento: " . $conn->error;
            }
            $stmt->close();
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}

// Busca os dados atuais do agendamento
$stmt = $conn->prepare("SELECT * FROM ferias WHERE id = ?");
$stmt->bind_param("i", $registro_id);
$stmt->execute();
$dados_ferias = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dados_ferias) {
    header("Location: index.php?id=ferias");
    exit;
}
?>

<section class="page-header">
    <h2>Editar Agendamento de Férias</h2>
    <a href="?id=ferias" class="btn-primary" style="background: var(--muted); text-decoration: none;">Voltar</a>
</section>

<section class="table-card">
    <?php if ($erro): ?>
        <div style="background: #fef2f2; color: var(--danger); border: 1px solid #fca5a5; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <form action="?id=ferias_editar&registro=<?= $registro_id ?>" method="POST" style="max-width: 500px;">
        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Funcionário</label>
            <select name="funcionario_id" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
                <?php foreach ($funcionarios as $func): ?>
                    <option value="<?= htmlspecialchars($func['id']) ?>" <?= $dados_ferias['funcionario_id'] == $func['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($func['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Data de Início</label>
            <input type="date" name="data_inicio" value="<?= htmlspecialchars($dados_ferias['data_inicio']) ?>" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Data de Término</label>
            <input type="date" name="data_fim" value="<?= htmlspecialchars($dados_ferias['data_fim']) ?>" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Status do Agendamento</label>
            <select name="status" style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
                <option value="Agendada" <?= $dados_ferias['status'] === 'Agendada' ? 'selected' : '' ?>>Agendada</option>
                <option value="Em andamento" <?= $dados_ferias['status'] === 'Em andamento' ? 'selected' : '' ?>>Em andamento</option>
                <option value="Concluída" <?= $dados_ferias['status'] === 'Concluída' ? 'selected' : '' ?>>Concluída</option>
                <option value="Cancelada" <?= $dados_ferias['status'] === 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
            </select>
        </div>

        <button type="submit" class="btn-primary">Salvar Alterações</button>
    </form>
</section>