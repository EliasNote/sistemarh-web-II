<?php
require_once 'conexao.php';
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
    <div>
        <button onclick="window.print()" class="btn-primary" style="background: #1e293b; margin-right: 8px;">Imprimir</button>
        <a href="?id=folha" class="btn-primary" style="background: var(--muted); text-decoration: none;">Voltar</a>
    </div>
</section>

<section class="table-card" style="max-width: 700px; margin: 0 auto; padding: 30px; font-family: monospace; border: 2px solid #000; background: #fff;">
    <div style="text-align: center; border-bottom: 2px double #000; padding-bottom: 15px; margin-bottom: 20px;">
        <h3 style="margin: 0; font-size: 1.4rem;">SISTEMA RH</h3>
        <p style="margin: 5px 0 0;">Demonstrativo de Pagamento de Salário</p>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 20px; font-size: 0.95rem;">
        <div>
            <strong>Funcionário:</strong> <?= htmlspecialchars($folha['funcionario_nome']) ?><br>
            <strong>Cargo:</strong> <?= htmlspecialchars($folha['cargo_nome']) ?><br>
            <strong>Setor:</strong> <?= htmlspecialchars($folha['setor']) ?>
        </div>
        <div style="text-align: right;">
            <strong>Referência:</strong> <?= sprintf('%02d/%d', $folha['mes'], $folha['ano']) ?><br>
            <strong>Folha ID:</strong> #<?= htmlspecialchars($folha['id']) ?>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; min-width: auto; font-size: 0.95rem;">
        <thead>
            <tr style="border-bottom: 1px solid #000; border-top: 1px solid #000;">
                <th style="background: none; text-align: left; padding: 8px 0;">Descrição</th>
                <th style="background: none; text-align: right; padding: 8px 0;">Vencimentos (R$)</th>
                <th style="background: none; text-align: right; padding: 8px 0;">Descontos (R$)</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom: 1px dashed #ccc;">
                <td style="padding: 10px 0;">Salário Base</td>
                <td style="text-align: right; padding: 10px 0;"><?= number_format($folha['salario_bruto'], 2, ',', '.') ?></td>
                <td style="text-align: right; padding: 10px 0;">-</td>
            </tr>
            <tr style="border-bottom: 1px dashed #ccc;">
                <td style="padding: 10px 0;">Previdência Social (INSS)</td>
                <td style="text-align: right; padding: 10px 0;">-</td>
                <td style="text-align: right; padding: 10px 0;"><?= number_format($folha['desconto_inss'], 2, ',', '.') ?></td>
            </tr>
            <?php if ($folha['desconto_irpf'] > 0): ?>
                <tr style="border-bottom: 1px dashed #ccc;">
                    <td style="padding: 10px 0;">Imposto de Renda Retido na Fonte (IRPF)</td>
                    <td style="text-align: right; padding: 10px 0;">-</td>
                    <td style="text-align: right; padding: 10px 0;"><?= number_format($folha['desconto_irpf'], 2, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
            <?php if ($folha['outros_descontos'] > 0): ?>
                <tr style="border-bottom: 1px dashed #ccc;">
                    <td style="padding: 10px 0;">Outros Descontos</td>
                    <td style="text-align: right; padding: 10px 0;">-</td>
                    <td style="text-align: right; padding: 10px 0;"><?= number_format($folha['outros_descontos'], 2, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="border-top: 1px solid #000; padding-top: 15px; display: grid; grid-template-columns: 1fr 1fr; font-size: 1rem; margin-bottom: 15px;">
        <div>
            <strong>TOTAL VENCIMENTOS:</strong> R$ <?= number_format($folha['salario_bruto'], 2, ',', '.') ?>
        </div>
        <div style="text-align: right;">
            <strong>TOTAL DESCONTOS:</strong> R$ <?= number_format($folha['total_descontos'], 2, ',', '.') ?>
        </div>
    </div>

    <div style="padding: 15px; background: #f3f4f6; border: 1px solid #000; text-align: right; font-size: 1.15rem; margin-bottom: 20px;">
        <strong>VALOR LÍQUIDO CREDITADO: R$ <?= number_format($folha['salario_liquido'], 2, ',', '.') ?></strong>
    </div>

    <!-- Informações de base e depósito de FGTS -->
    <div style="border-top: 2px double #000; padding-top: 10px; display: grid; grid-template-columns: 1fr 1fr; font-size: 0.85rem; color: #374151;">
        <div>
            <strong>Base de Cálculo FGTS:</strong> R$ <?= number_format($folha['salario_bruto'], 2, ',', '.') ?>
        </div>
        <div style="text-align: right;">
            <strong>Depósito FGTS do Mês (8%):</strong> R$ <?= number_format($folha['valor_fgts'], 2, ',', '.') ?>
        </div>
    </div>
</section>