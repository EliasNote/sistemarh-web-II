<?php
require_once 'conexao.php';
$erro = '';

// Busca todos os funcionários ativos para popular a seleção
$resultado_func = $conn->query("SELECT id, nome FROM funcionarios WHERE status != 'Inativo' ORDER BY nome ASC");
$funcionarios = $resultado_func ? $resultado_func->fetch_all(MYSQLI_ASSOC) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $funcionario_id = $_POST['funcionario_id'] ?? '';
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';
    $status = $_POST['status'] ?? 'Agendada';

    if (!empty($funcionario_id) && !empty($data_inicio) && !empty($data_fim)) {
        // Validação: data de início não pode ser maior que a de término
        if (strtotime($data_inicio) > strtotime($data_fim)) {
            $erro = "A data de início não pode ser maior do que a data de término das férias.";
        } else {
            $stmt = $conn->prepare("INSERT INTO ferias (funcionario_id, data_inicio, data_fim, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $funcionario_id, $data_inicio, $data_fim, $status);

            if ($stmt->execute()) {
                header("Location: index.php?id=ferias");
                exit;
            } else {
                $erro = "Erro ao registrar agendamento: " . $conn->error;
            }
            $stmt->close();
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}
?>

<section class="page-header">
    <h2>Agendar Férias</h2>
    <a href="?id=ferias" class="btn-primary" style="background: var(--muted); text-decoration: none;">Voltar</a>
</section>

<section class="table-card">
    <?php if ($erro): ?>
        <div style="background: #fef2f2; color: var(--danger); border: 1px solid #fca5a5; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <form action="?id=ferias_cadastro" method="POST" style="max-width: 500px;">
        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Funcionário</label>
            <select name="funcionario_id" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
                <option value="">Selecione o colaborador...</option>
                <?php foreach ($funcionarios as $func): ?>
                    <option value="<?= htmlspecialchars($func['id']) ?>"><?= htmlspecialchars($func['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Data de Início</label>
            <input type="date" name="data_inicio" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Data de Término</label>
            <input type="date" name="data_fim" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Status do Agendamento</label>
            <select name="status" style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
                <option value="Agendada">Agendada</option>
                <option value="Em andamento">Em andamento</option>
                <option value="Concluída">Concluída</option>
                <option value="Cancelada">Cancelada</option>
            </select>
        </div>

        <button type="submit" class="btn-primary">Registrar Férias</button>
    </form>
</section>