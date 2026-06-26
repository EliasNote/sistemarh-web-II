<?php
require_once './config/conexao.php';

$query = "SELECT f.*, c.nome AS cargo_nome FROM funcionarios f LEFT JOIN cargos c ON f.cargo_id = c.id ORDER BY f.id DESC";
$resultado = $conn->query($query);
$funcionarios = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
?>

<section class="page-header">
    <h2>Funcionários</h2>
    <a href="?id=funcionario_cadastro" class="btn-primary btn-primary--link">+ Novo Funcionário</a>
</section>

<section class="table-card">
    <div class="table-card__top">
        <p>Lista de colaboradores cadastrados</p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Cargo</th>
                    <th>Setor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($funcionarios)): ?>
                    <tr>
                        <td colspan="6" class="table-empty">Nenhum funcionário cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($funcionarios as $funcionario): ?>
                        <tr>
                            <td><?= htmlspecialchars($funcionario['id']) ?></td>
                            <td><?= htmlspecialchars($funcionario['nome']) ?></td>
                            <td><?= htmlspecialchars($funcionario['cargo_nome'] ?? 'Sem cargo') ?></td>
                            <td><?= htmlspecialchars($funcionario['setor']) ?></td>
                            <td>
                                <span class="status <?= $funcionario['status'] === 'Ativo' ? 'ativo' : 'ferias' ?>">
                                    <?= htmlspecialchars($funcionario['status']) ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="?id=funcionario_editar&registro=<?= $funcionario['id'] ?>" class="btn-link btn-link--plain">Editar</a>
                                <a href="?id=funcionario_excluir&registro=<?= $funcionario['id'] ?>" class="btn-link danger btn-link--plain" onclick="return confirm('Tem certeza que deseja excluir este funcionário?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>