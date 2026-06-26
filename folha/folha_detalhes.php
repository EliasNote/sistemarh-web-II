<?php
require_once './config/conexao.php';
$registro_id = $_GET['registro'] ?? '';

$query = "SELECT fp.*, f.nome AS funcionario_nome, f.setor, c.nome AS cargo_nome 
          FROM folha_pagamento fp 
          JOIN funcionarios f ON fp.funcionario_id = f.id 
          JOIN cargos c ON f.cargo_id = c.id 
          WHERE fp.id = ?";
        
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $registro_id);
$stmt->execute();
$folha = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$folha) {
    header("Location: index.php?id=folha");
    exit;
}
?>

<section class="page-header">
    <h2>Holerite Digital</h2>
    <div class="page-actions">
        <button onclick="window.print()" class="btn-primary btn-primary--dark">Imprimir</button>
        <a href="?id=folha" class="btn-primary btn-primary--muted btn-primary--link">Voltar</a>
    </div>
</section>

<section class="table-card folha-holerite">
    <div class="holerite-print-header">
        <h3>SISTEMA RH</h3>
        <p>Demonstrativo de Pagamento de Salário</p>
    </div>

    <div class="holerite-print-meta">
        <div>
            <strong>Funcionário:</strong> <?= htmlspecialchars($folha['funcionario_nome']) ?><br>
            <strong>Cargo:</strong> <?= htmlspecialchars($folha['cargo_nome']) ?><br>
            <strong>Setor:</strong> <?= htmlspecialchars($folha['setor']) ?>
        </div>
        <div class="text-right">
            <strong>Referência:</strong> <?= sprintf('%02d/%d', $folha['mes'], $folha['ano']) ?><br>
            <strong>Folha ID:</strong> #<?= htmlspecialchars($folha['id']) ?>
        </div>
    </div>

    <table class="holerite-print-table">
        <thead>
            <tr class="holerite-print-headrow">
                <th class="col-description">Descrição</th>
                <th class="col-money">Vencimentos (R$)</th>
                <th class="col-money">Descontos (R$)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="holerite-print-row">
                <td class="cell-padding">Salário Base</td>
                <td class="text-right cell-padding"><?= number_format($folha['salario_bruto'], 2, ',', '.') ?></td>
                <td class="text-right cell-padding">-</td>
            </tr>
            <tr class="holerite-print-row">
                <td class="cell-padding">Previdência Social (INSS)</td>
                <td class="text-right cell-padding">-</td>
                <td class="text-right cell-padding"><?= number_format($folha['desconto_inss'], 2, ',', '.') ?></td>
            </tr>
            <?php if ($folha['desconto_irpf'] > 0): ?>
                <tr class="holerite-print-row">
                    <td class="cell-padding">Imposto de Renda Retido na Fonte (IRPF)</td>
                    <td class="text-right cell-padding">-</td>
                    <td class="text-right cell-padding"><?= number_format($folha['desconto_irpf'], 2, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
            <?php if ($folha['outros_descontos'] > 0): ?>
                <tr class="holerite-print-row">
                    <td class="cell-padding">Outros Descontos</td>
                    <td class="text-right cell-padding">-</td>
                    <td class="text-right cell-padding"><?= number_format($folha['outros_descontos'], 2, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="holerite-print-totalizer">
        <div>
            <strong>TOTAL VENCIMENTOS:</strong> R$ <?= number_format($folha['salario_bruto'], 2, ',', '.') ?>
        </div>
        <div class="text-right">
            <strong>TOTAL DESCONTOS:</strong> R$ <?= number_format($folha['total_descontos'], 2, ',', '.') ?>
        </div>
    </div>

    <div class="holerite-print-liquid">
        <strong>VALOR LÍQUIDO CREDITADO: R$ <?= number_format($folha['salario_liquido'], 2, ',', '.') ?></strong>
    </div>

    <div class="holerite-print-footer">
        <div>
            <strong>Base de Cálculo FGTS:</strong> R$ <?= number_format($folha['salario_bruto'], 2, ',', '.') ?>
        </div>
        <div class="text-right">
            <strong>Depósito FGTS do Mês (8%):</strong> R$ <?= number_format($folha['valor_fgts'], 2, ',', '.') ?>
        </div>
    </div>
</section>