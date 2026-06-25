<?php
require_once './config/conexao.php';
$resultado = $conn->query("SELECT * FROM cargos ORDER BY nome ASC");
$cargos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
?>

<section class="page-header">
    <h2>Cargos</h2>
    <a href="?id=cargo_cadastro" class="btn-primary" style="text-decoration: none;">+ Novo Cargo</a>
</section>

<section class="table-card">
    <div class="table-card__top">
        <p>Lista de cargos disponíveis</p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Salário Base (R$)</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cargos)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Nenhum cargo cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cargos as $cargo): ?>
                        <tr>
                            <td><?= htmlspecialchars($cargo['id']) ?></td>
                            <td><?= htmlspecialchars($cargo['nome']) ?></td>
                            <td><?= number_format($cargo['salario_base'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($cargo['descricao']) ?></td>
                            <td class="actions">
                                <a href="?id=cargo_editar&registro=<?= $cargo['id'] ?>" class="btn-link" style="text-decoration: none;">Editar</a>
                                <a href="?id=cargo_excluir&registro=<?= $cargo['id'] ?>" class="btn-link danger" style="text-decoration: none;" onclick="return confirm('Tem certeza que deseja excluir este cargo?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>