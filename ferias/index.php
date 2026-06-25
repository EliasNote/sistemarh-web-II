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
    <a href="?id=ferias_cadastro" class="btn-primary" style="text-decoration: none;">+ Agendar Férias</a>
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
                        <td colspan="8" style="text-align: center;">Nenhum agendamento de férias registrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ferias as $f): 
                        $status_style = '';
                        $status_exibicao = $f['status'];

                        if ($f['status'] === 'Agendada') {
                            $status_style = 'background: #fef3c7; color: #92400e;';
                        } elseif ($f['status'] === 'Em andamento') {
                            $status_style = 'background: #dcfce7; color: #166534;';
                        } elseif ($f['status'] === 'Concluida') {
                            $status_style = 'background: #e5e7eb; color: #374151;';
                            $status_exibicao = 'Concluída';
                        } elseif ($f['status'] === 'Cancelada') {
                            $status_style = 'background: #fef2f2; color: #b91c1c;';
                        }
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($f['id']) ?></td>
                            <td><?= htmlspecialchars($f['funcionario_nome']) ?></td>
                            <td><?= date('d/m/Y', strtotime($f['data_inicio'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($f['data_fim'])) ?></td>
                            <td><?= htmlspecialchars($f['dias']) ?> dias</td>
                            <td>
                                <!-- Carrega os valores financeiros salvos e editados pelo gestor -->
                                <div style="font-size: 0.9rem; line-height: 1.4;">
                                    <strong style="color: #166534;">Líquido: R$ <?= number_format($f['valor_liquido'], 2, ',', '.') ?></strong><br>
                                    <small style="color: var(--muted); display: block; margin-top: 2px;">
                                        Total Bruto: R$ <?= number_format($f['valor_bruto'], 2, ',', '.') ?>
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="status" style="<?= $status_style ?>">
                                    <?= htmlspecialchars($status_exibicao) ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="?id=ferias_detalhes&registro=<?= $f['id'] ?>" class="btn-link" style="text-decoration: none; color: #0f766e; font-weight: bold; margin-right: 8px;">Ver Recibo</a>
                                <a href="?id=ferias_editar&registro=<?= $f['id'] ?>" class="btn-link" style="text-decoration: none; margin-right: 8px;">Editar</a>
                                <a href="?id=ferias_excluir&registro=<?= $f['id'] ?>" class="btn-link danger" style="text-decoration: none;" onclick="return confirm('Tem certeza que deseja excluir este agendamento?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>