<?php
require_once './config/conexao.php';
$erro = '';

$resultado_cargos = $conn->query("SELECT id, nome, salario_base FROM cargos ORDER BY nome ASC");
$cargos = $resultado_cargos ? $resultado_cargos->fetch_all(MYSQLI_ASSOC) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $cargo_id = $_POST['cargo_id'] ?? '';
    $salario_base = $_POST['salario_base'] ?? '';
    $setor = $_POST['setor'] ?? '';
    $data_contratacao = $_POST['data_contratacao'] ?? '';
    $status = $_POST['status'] ?? 'Ativo';

    if (!empty($nome) && !empty($cargo_id) && !empty($salario_base) && !empty($setor) && !empty($data_contratacao)) {
        $stmt = $conn->prepare("INSERT INTO funcionarios (nome, cargo_id, salario_base, setor, data_contratacao, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sidsss", $nome, $cargo_id, $salario_base, $setor, $data_contratacao, $status);

        if ($stmt->execute()) {
            header("Location: index.php?id=funcionarios");
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
    <h2>Novo Funcionário</h2>
    <a href="?id=funcionarios" class="btn-primary" style="background: var(--muted); text-decoration: none;">Voltar</a>
</section>

<section class="table-card">
    <?php if ($erro): ?>
        <div style="background: #fef2f2; color: var(--danger); border: 1px solid #fca5a5; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <form action="?id=funcionario_cadastro" method="POST" style="max-width: 500px;">
        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Nome</label>
            <input type="text" name="nome" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Cargo</label>
            <select name="cargo_id" id="cargo_id" required onchange="aplicarSalarioSugerido()" style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
                <option value="">Selecione um cargo...</option>
                <?php foreach ($cargos as $cargo): ?>
                    <option value="<?= htmlspecialchars($cargo['id']) ?>" data-sugestao="<?= htmlspecialchars($cargo['salario_base']) ?>">
                        <?= htmlspecialchars($cargo['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Salário Base (R$)</label>
            <input type="number" step="0.01" name="salario_base" id="salario_base" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Setor</label>
            <input type="text" name="setor" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Data de Contratação</label>
            <input type="date" name="data_contratacao" required style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 600;">Status</label>
            <select name="status" style="width: 100%; padding: 9px 12px; border: 1px solid var(--line); border-radius: 8px; box-sizing: border-box;">
                <option value="Ativo">Ativo</option>
                <option value="Férias">Férias</option>
            </select>
        </div>

        <button type="submit" class="btn-primary">Salvar Cadastro</button>
    </form>
</section>

<script>
function aplicarSalarioSugerido() {
    const select = document.getElementById('cargo_id');
    const option = select.options[select.selectedIndex];
    const sugestao = option.getAttribute('data-sugestao');
    if (sugestao) {
        document.getElementById('salario_base').value = parseFloat(sugestao).toFixed(2);
    }
}
</script>