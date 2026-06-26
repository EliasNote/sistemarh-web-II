<?php
require_once './config/conexao.php';
$erro = '';
$registro_id = $_GET['registro'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $cargo_id = $_POST['cargo_id'] ?? '';
    $salario_base = $_POST['salario_base'] ?? '';
    $setor = $_POST['setor'] ?? '';
    $data_contratacao = $_POST['data_contratacao'] ?? '';
    $status = $_POST['status'] ?? 'Ativo';

    if (!empty($nome) && !empty($cargo_id) && !empty($salario_base) && !empty($setor) && !empty($data_contratacao)) {
        $stmt = $conn->prepare("UPDATE funcionarios SET nome=?, cargo_id=?, salario_base=?, setor=?, data_contratacao=?, status=? WHERE id=?");
        $stmt->bind_param("sidsssi", $nome, $cargo_id, $salario_base, $setor, $data_contratacao, $status, $registro_id);

        if ($stmt->execute()) {
            header("Location: index.php?id=funcionarios");
            exit;
        } else {
            $erro = "Erro ao atualizar: " . $conn->error;
        }
        $stmt->close();
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}

$cargos = $conn->query("SELECT id, nome FROM cargos ORDER BY nome ASC")->fetch_all(MYSQLI_ASSOC);

$stmt = $conn->prepare("SELECT * FROM funcionarios WHERE id = ?");
$stmt->bind_param("i", $registro_id);
$stmt->execute();
$funcionario = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$funcionario) {
    header("Location: index.php?id=funcionarios");
    exit;
}
?>

<section class="page-header">
    <h2>Editar Funcionário</h2>
    <a href="?id=funcionarios" class="btn-primary btn-primary--muted btn-primary--link">Voltar</a>
</section>

<section class="table-card">
    <?php if ($erro): ?>
        <div class="alert-error">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <form action="?id=funcionario_editar&registro=<?= $registro_id ?>" method="POST" class="employee-form">
        <div class="form-field">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($funcionario['nome']) ?>" required class="form-control">
        </div>

        <div class="form-field">
            <label class="form-label">Cargo</label>
            <select name="cargo_id" required class="form-control">
                <?php foreach ($cargos as $cargo): ?>
                    <option value="<?= htmlspecialchars($cargo['id']) ?>" <?= $funcionario['cargo_id'] == $cargo['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cargo['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-field">
            <label class="form-label">Salário Base (R$)</label>
            <input type="number" step="0.01" name="salario_base" value="<?= htmlspecialchars($funcionario['salario_base']) ?>" required class="form-control">
        </div>

        <div class="form-field">
            <label class="form-label">Setor</label>
            <input type="text" name="setor" value="<?= htmlspecialchars($funcionario['setor']) ?>" required class="form-control">
        </div>

        <div class="form-field">
            <label class="form-label">Data de Contratação</label>
            <input type="date" name="data_contratacao" value="<?= htmlspecialchars($funcionario['data_contratacao']) ?>" required class="form-control">
        </div>

        <div class="form-field form-field--last">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="Ativo" <?= $funcionario['status'] === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                <option value="Férias" <?= $funcionario['status'] === 'Férias' ? 'selected' : '' ?>>Férias</option>
            </select>
        </div>

        <button type="submit" class="btn-primary">Salvar Alterações</button>
    </form>
</section>