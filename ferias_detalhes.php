<?php
require_once 'conexao.php';
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
    <div>
        <button onclick="window.print()" class="btn-primary" style="background: #1e293b; margin-right: 8px;">Imprimir</button>
        <a href="?id=ferias" class="btn-primary" style="background: var(--muted); text-decoration: none;">Voltar</a>
    </div>
</section>

<section class="table-card" style="max-width: 700px; margin: 0 auto; padding: 30px; font-family: monospace; border: 2px solid #000; background: #fff;">
    <div style="text-align: center; border-bottom: 2px double #000; padding-bottom: 15px; margin-bottom: 20px;">
        <h3 style="margin: 0; font-size: 1.4rem;">SISTEMA RH</h3>
        <p style="margin: 5px 0 0;">Aviso e Recibo de Férias (CLT Art. 145)</p>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 20px; font-size: 0.95rem; line-height: 1.4;">
        <div>
            <strong>Funcionário:</strong> <?= htmlspecialchars($dados['funcionario_nome']) ?><br>
            <strong>Cargo:</strong> <?= htmlspecialchars($dados['cargo_nome']) ?><br>
            <strong>Setor:</strong> <?= htmlspecialchars($dados['setor']) ?><br>
            <strong>Salário Base Contratual:</strong> R$ <?= number_format($dados['salario_base'], 2, ',', '.') ?>
        </div>
        <div style="text-align: right;">
            <strong>Período de Gozo:</strong><br>
            <?= date('d/m/Y', strtotime($dados['data_inicio'])) ?> a <?= date('d/m/Y', strtotime($dados['data_fim'])) ?><br>
            <strong>Total de Dias:</strong> <?= htmlspecialchars($dados['dias']) ?> dias<br>
            <strong>Status:</strong> <?= htmlspecialchars($dados['status'] === 'Concluida' ? 'Concluída' : $dados['status']) ?>
        </div>
    </div>

    <div style="background: #f9fafb; border: 1px solid #d1d5db; border-radius: 6px; padding: 12px; font-size: 0.85rem; margin-bottom: 20px; line-height: 1.4;">
        <div style="display: flex; justify-content: space-between;">
            <span>📅 <strong>Data de Emissão do Aviso (30 dias antes):</strong></span>
            <span><?= date('d/m/Y', strtotime($data_aviso)) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 4px;">
            <span>💰 <strong>Data Limite de Pagamento (2 dias antes):</strong></span>
            <span><?= date('d/m/Y', strtotime($data_pagamento)) ?></span>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; min-width: auto; font-size: 0.95rem;">
        <thead>
            <tr style="border-bottom: 1px solid #000; border-top: 1px solid #000;">
                <th style="background: none; text-align: left; padding: 8px 0; width: 50%;">Descrição das Rubricas</th>
                <th style="background: none; text-align: right; padding: 8px 0; width: 25%;">Vencimentos (R$)</th>
                <th style="background: none; text-align: right; padding: 8px 0; width: 25%;">Descontos (R$)</th>
            </tr>
        </thead>
        <tbody>
            <tr style="border-bottom: 1px dashed #ccc;">
                <td style="padding: 10px 0;">Férias Proporcionais (<?= htmlspecialchars($dados['dias']) ?> dias)</td>
                <td style="text-align: right; padding: 10px 0;"><?= number_format($valor_proporcional, 2, ',', '.') ?></td>
                <td style="text-align: right; padding: 10px 0;">-</td>
            </tr>
            <tr style="border-bottom: 1px dashed #ccc;">
                <td style="padding: 10px 0;">1/3 Constitucional sobre Férias</td>
                <td style="text-align: right; padding: 10px 0;"><?= number_format($terco_constitucional, 2, ',', '.') ?></td>
                <td style="text-align: right; padding: 10px 0;">-</td>
            </tr>
            <tr style="border-bottom: 1px dashed #ccc;">
                <td style="padding: 10px 0;">Contribuição INSS sobre Férias (Retido)</td>
                <td style="text-align: right; padding: 10px 0;">-</td>
                <td style="text-align: right; padding: 10px 0;"><?= number_format($desconto_inss, 2, ',', '.') ?></td>
            </tr>
            <?php if ($desconto_irpf > 0): ?>
                <tr style="border-bottom: 1px dashed #ccc;">
                    <td style="padding: 10px 0;">Imposto de Renda Retido (IRPF sobre Férias)</td>
                    <td style="text-align: right; padding: 10px 0;">-</td>
                    <td style="text-align: right; padding: 10px 0;"><?= number_format($desconto_irpf, 2, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="border-top: 1px solid #000; padding-top: 15px; display: grid; grid-template-columns: 1fr 1fr; font-size: 1rem; margin-bottom: 15px;">
        <div>
            <strong>TOTAL BRUTO (Vencimentos):</strong> R$ <?= number_format($salario_bruto_ferias, 2, ',', '.') ?>
        </div>
        <div style="text-align: right;">
            <strong>TOTAL RETENÇÕES (Descontos):</strong> R$ <?= number_format($total_descontos, 2, ',', '.') ?>
        </div>
    </div>

    <div style="padding: 15px; background: #f3f4f6; border: 1px solid #000; text-align: right; font-size: 1.15rem; margin-bottom: 30px;">
        <strong>VALOR LÍQUIDO A RECEBER: R$ <?= number_format($salario_liquido_ferias, 2, ',', '.') ?></strong>
    </div>

    <div style="margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px; font-size: 0.8rem; text-align: center;">
        <div style="border-top: 1px solid #000; padding-top: 8px; margin-top: 20px;">
            Assinatura do Empregador / Empresa
        </div>
        <div style="border-top: 1px solid #000; padding-top: 8px; margin-top: 20px;">
            Assinatura do Colaborador (Recebi em: ____/____/____)
        </div>
    </div>
</section>