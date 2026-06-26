<?php
require_once './config/conexao.php';

$query = "SELECT f.*, func.nome AS funcionario_nome 
          FROM ferias f 
          JOIN funcionarios func ON f.funcionario_id = func.id 
          ORDER BY f.data_inicio DESC";

$resultado = $conn->query($query);
$ferias = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
?>

<section class="page-header">
    <h2>Agendamento de Férias</h2>
    <a href="?id=ferias_cadastro" class="btn-primary btn-primary--link">+ Agendar Férias</a>
</section>

<section class="table-card">
    <div class="table-card__top">
        <p>Controle e Cálculo Financeiro de Férias (CLT)</p>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Funcionário</th>
                    <th>Início</th>
                    <th>Fim</th>
                    <th>Dias</th>
                    <th>Cálculo Financeiro (CLT)</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ferias)): ?>
                    <tr>
                        <td colspan="8" class="table-empty">Nenhum agendamento de férias registrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ferias as $f): 
                        $status_exibicao = $f['status'];
                        $status_class = 'status--agendada';

                        if ($f['status'] === 'Agendada') {
                            $status_class = 'status--agendada';
                        } elseif ($f['status'] === 'Em andamento') {
                            $status_class = 'status--andamento';
                        } elseif ($f['status'] === 'Concluida') {
                            $status_class = 'status--concluida';
                            $status_exibicao = 'Concluída';
                        } elseif ($f['status'] === 'Cancelada') {
                            $status_class = 'status--cancelada';
                        }
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($f['id']) ?></td>
                            <td><?= htmlspecialchars($f['funcionario_nome']) ?></td>
                            <td><?= date('d/m/Y', strtotime($f['data_inicio'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($f['data_fim'])) ?></td>
                            <td><?= htmlspecialchars($f['dias']) ?> dias</td>
                            <td class="finance-summary">
                                <strong class="finance-summary__liquido">Líquido: R$ <?= number_format($f['valor_liquido'], 2, ',', '.') ?></strong>
                                <small class="finance-summary__bruto">Total Bruto: R$ <?= number_format($f['valor_bruto'], 2, ',', '.') ?></small>
                            </td>
                            <td>
                                <span class="status <?= $status_class ?>">
                                    <?= htmlspecialchars($status_exibicao) ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="?id=ferias_detalhes&registro=<?= $f['id'] ?>" class="btn-link btn-link--emphasis">Ver Recibo</a>
                                <a href="?id=ferias_editar&registro=<?= $f['id'] ?>" class="btn-link btn-link--plain">Editar</a>
                                <a href="?id=ferias_excluir&registro=<?= $f['id'] ?>" class="btn-link danger btn-link--plain" onclick="return confirm('Tem certeza que deseja excluir este agendamento?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>