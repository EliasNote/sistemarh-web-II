<?php
require_once './config/conexao.php';
$registro_id = $_GET['registro'] ?? '';

$query = "SELECT fer.*, f.nome AS funcionario_nome, f.setor, f.salario_base, c.nome AS cargo_nome 
          FROM ferias fer 
          JOIN funcionarios f ON fer.funcionario_id = f.id 
          JOIN cargos c ON f.cargo_id = c.id 
          WHERE fer.id = ?";
        
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $registro_id);
$stmt->execute();
$dados = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dados) {
    header("Location: index.php?id=ferias");
    exit;
}

// Recupera os valores salvos no banco de dados [1]
$valor_diario = $dados['salario_base'] / 30;
$valor_proporcional = $valor_diario * $dados['dias'];
$terco_constitucional = $valor_proporcional / 3;

$desconto_inss = $dados['desconto_inss'];
$desconto_irpf = $dados['desconto_irpf'];
$salario_bruto_ferias = $dados['valor_bruto'];
$total_descontos = $desconto_inss + $desconto_irpf;
$salario_liquido_ferias = $dados['valor_liquido'];

$data_aviso = date('Y-m-d', strtotime($dados['data_inicio'] . ' -30 days'));
$data_pagamento = date('Y-m-d', strtotime($dados['data_inicio'] . ' -2 days'));
?>

<section class="page-header">
    <h2>Recibo e Aviso de Férias</h2>
    <div class="page-actions">
        <button onclick="window.print()" class="btn-primary btn-primary--dark">Imprimir</button>
        <a href="?id=ferias" class="btn-primary btn-primary--muted btn-primary--link">Voltar</a>
    </div>
</section>

<section class="table-card ferias-holerite">
    <div class="ferias-print-header">
        <h3>SISTEMA RH</h3>
        <p>Aviso e Recibo de Férias (CLT Art. 145)</p>
    </div>

    <div class="ferias-print-meta">
        <div>
            <strong>Funcionário:</strong> <?= htmlspecialchars($dados['funcionario_nome']) ?><br>
            <strong>Cargo:</strong> <?= htmlspecialchars($dados['cargo_nome']) ?><br>
            <strong>Setor:</strong> <?= htmlspecialchars($dados['setor']) ?><br>
            <strong>Salário Base Contratual:</strong> R$ <?= number_format($dados['salario_base'], 2, ',', '.') ?>
        </div>
        <div class="text-right">
            <strong>Período de Gozo:</strong><br>
            <?= date('d/m/Y', strtotime($dados['data_inicio'])) ?> a <?= date('d/m/Y', strtotime($dados['data_fim'])) ?><br>
            <strong>Total de Dias:</strong> <?= htmlspecialchars($dados['dias']) ?> dias<br>
            <strong>Status:</strong> <?= htmlspecialchars($dados['status'] === 'Concluida' ? 'Concluída' : $dados['status']) ?>
        </div>
    </div>

    <div class="ferias-note-box">
        <div class="ferias-note-row">
            <span>📅 <strong>Data de Emissão do Aviso (30 dias antes):</strong></span>
            <span><?= date('d/m/Y', strtotime($data_aviso)) ?></span>
        </div>
        <div class="ferias-note-row ferias-note-row--spaced">
            <span>💰 <strong>Data Limite de Pagamento (2 dias antes):</strong></span>
            <span><?= date('d/m/Y', strtotime($data_pagamento)) ?></span>
        </div>
    </div>

    <table class="ferias-print-table">
        <thead>
            <tr class="ferias-print-headrow">
                <th class="col-description">Descrição das Rubricas</th>
                <th class="col-money">Vencimentos (R$)</th>
                <th class="col-money">Descontos (R$)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="ferias-print-row">
                <td class="cell-padding">Férias Proporcionais (<?= htmlspecialchars($dados['dias']) ?> dias)</td>
                <td class="cell-padding text-right"><?= number_format($valor_proporcional, 2, ',', '.') ?></td>
                <td class="cell-padding text-right">-</td>
            </tr>
            <tr class="ferias-print-row">
                <td class="cell-padding">1/3 Constitucional sobre Férias</td>
                <td class="cell-padding text-right"><?= number_format($terco_constitucional, 2, ',', '.') ?></td>
                <td class="cell-padding text-right">-</td>
            </tr>
            <tr class="ferias-print-row">
                <td class="cell-padding">Contribuição INSS sobre Férias (Retido)</td>
                <td class="cell-padding text-right">-</td>
                <td class="cell-padding text-right"><?= number_format($desconto_inss, 2, ',', '.') ?></td>
            </tr>
            <?php if ($desconto_irpf > 0): ?>
                <tr class="ferias-print-row">
                    <td class="cell-padding">Imposto de Renda Retido (IRPF sobre Férias)</td>
                    <td class="cell-padding text-right">-</td>
                    <td class="cell-padding text-right"><?= number_format($desconto_irpf, 2, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="ferias-print-totalizer">
        <div>
            <strong>TOTAL BRUTO (Vencimentos):</strong> R$ <?= number_format($salario_bruto_ferias, 2, ',', '.') ?>
        </div>
        <div class="text-right">
            <strong>TOTAL RETENÇÕES (Descontos):</strong> R$ <?= number_format($total_descontos, 2, ',', '.') ?>
        </div>
    </div>

    <div class="ferias-print-liquid">
        <strong>VALOR LÍQUIDO A RECEBER: R$ <?= number_format($salario_liquido_ferias, 2, ',', '.') ?></strong>
    </div>

    <div class="ferias-signatures">
        <div class="ferias-signature-line">
            Assinatura do Empregador / Empresa
        </div>
        <div class="ferias-signature-line">
            Assinatura do Colaborador (Recebi em: ____/____/____)
        </div>
    </div>
</section>