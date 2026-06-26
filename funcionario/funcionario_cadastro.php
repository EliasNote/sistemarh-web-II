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
    <a href="?id=funcionarios" class="btn-primary btn-primary--muted btn-primary--link">Voltar</a>
</section>

<section class="table-card">
    <?php if ($erro): ?>
        <div class="alert-error">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <form action="?id=funcionario_cadastro" method="POST" class="employee-form">
        <div class="form-field">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" required class="form-control">
        </div>

        <div class="form-field">
            <label class="form-label">Cargo</label>
            <select name="cargo_id" id="cargo_id" required onchange="aplicarSalarioSugerido()" class="form-control">
                <option value="">Selecione um cargo...</option>
                <?php foreach ($cargos as $cargo): ?>
                    <option value="<?= htmlspecialchars($cargo['id']) ?>" data-sugestao="<?= htmlspecialchars($cargo['salario_base']) ?>">
                        <?= htmlspecialchars($cargo['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-field">
            <label class="form-label">Salário Base (R$)</label>
            <input type="number" step="0.01" name="salario_base" id="salario_base" required class="form-control">
        </div>

        <div class="form-field">
            <label class="form-label">Setor</label>
            <input type="text" name="setor" required class="form-control">
        </div>

        <div class="form-field">
            <label class="form-label">Data de Contratação</label>
            <input type="date" name="data_contratacao" required class="form-control">
        </div>

        <div class="form-field form-field--last">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
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