<?php
require_once './config/conexao.php';

$query = "SELECT fp.*, f.nome AS funcionario_nome 
          FROM folha_pagamento fp 
          JOIN funcionarios f ON fp.funcionario_id = f.id 
          ORDER BY fp.ano DESC, fp.mes DESC, fp.id DESC";

$resultado = $conn->query($query);
$folhas = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
?>

<section class="page-header">
    <h2>Simulação de Folha de Pagamento</h2>
    <a href="?id=folha_gerar" class="btn-primary btn-primary--link">+ Gerar Nova Folha</a>
</section>

<section class="table-card">
    <div class="table-card__top">
        <p>Folhas de pagamento calculadas</p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Funcionário</th>
                    <th>Competência</th>
                    <th>Salário Bruto</th>
                    <th>Total Descontos</th>
                    <th>Salário Líquido</th>
                    <th>Geração</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($folhas)): ?>
                    <tr>
                        <td colspan="8" class="table-empty">Nenhuma simulação registrada.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($folhas as $f): ?>
                        <tr>
                            <td><?= htmlspecialchars($f['id']) ?></td>
                            <td><?= htmlspecialchars($f['funcionario_nome']) ?></td>
                            <td><?= sprintf('%02d/%d', $f['mes'], $f['ano']) ?></td>
                            <td>R$ <?= number_format($f['salario_bruto'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($f['total_descontos'], 2, ',', '.') ?></td>
                            <td class="salary-net">R$ <?= number_format($f['salario_liquido'], 2, ',', '.') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($f['data_geracao'])) ?></td>
                            <td class="actions">
                                <a href="?id=folha_detalhes&registro=<?= $f['id'] ?>" class="btn-link btn-link--plain">Ver Holerite</a>
                                <a href="?id=folha_excluir&registro=<?= $f['id'] ?>" class="btn-link danger btn-link--plain" onclick="return confirm('Excluir esta simulação?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>