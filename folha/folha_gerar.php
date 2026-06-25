<?php
require_once './config/conexao.php';
$erro = '';

// Busca funcionários e seus respectivos salários customizados
$query_func = "SELECT id, nome, salario_base FROM funcionarios WHERE status != 'Inativo' ORDER BY nome ASC";
$resultado_func = $conn->query($query_func);
$funcionarios = $resultado_func ? $resultado_func->fetch_all(MYSQLI_ASSOC) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $funcionario_id = $_POST['funcionario_id'] ?? '';
    $mes = $_POST['mes'] ?? '';
    $ano = $_POST['ano'] ?? '';
    $salario_bruto = floatval($_POST['salario_bruto'] ?? 0);
    $desconto_inss = floatval($_POST['desconto_inss'] ?? 0);
    $desconto_irpf = floatval($_POST['desconto_irpf'] ?? 0);
    $outros_descontos = !empty($_POST['outros_descontos']) ? floatval($_POST['outros_descontos']) : 0.00; // Não obrigatório
    $valor_fgts = floatval($_POST['valor_fgts'] ?? 0);

    if (!empty($funcionario_id) && !empty($mes) && !empty($ano)) {
        $stmt_check = $conn->prepare("SELECT id FROM folha_pagamento WHERE funcionario_id = ? AND mes = ? AND ano = ?");
        $stmt_check->bind_param("iii", $funcionario_id, $mes, $ano);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $erro = "Já existe uma folha cadastrada para este colaborador neste mês/ano.";
        } else {
            $stmt = $conn->prepare("INSERT INTO folha_pagamento (funcionario_id, mes, ano, salario_bruto, desconto_inss, desconto_irpf, outros_descontos, valor_fgts) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiddddd", $funcionario_id, $mes, $ano, $salario_bruto, $desconto_inss, $desconto_irpf, $outros_descontos, $valor_fgts);

            if ($stmt->execute()) {
                header("Location: index.php?id=folha");
                exit;
            } else {
                $erro = "Erro ao simular: " . $conn->error;
            }
            $stmt->close();
        }
        $stmt_check->close();
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}
?>

<style>
/* Estilização moderna e responsiva exclusiva para a simulação de folha */
.folha-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    margin-top: 20px;
}

@media (min-width: 992px) {
    .folha-grid {
        grid-template-columns: 1.2fr 0.8fr;
    }
}

.panel-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.panel-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #111827;
    margin-top: 0;
    margin-bottom: 20px;
    border-bottom: 1px solid #f3f4f6;
    padding-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.field-group {
    margin-bottom: 16px;
}

.field-group label {
    display: block;
    margin-bottom: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    color: #374151;
}

.field-group input, .field-group select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-sizing: border-box;
    font-family: inherit;
    font-size: 0.95rem;
    outline: none;
    transition: border-color 0.15s ease;
}

.field-group input:focus, .field-group select:focus {
    border-color: var(--primary);
}

/* Espelho do Holerite Interativo */
.holerite-preview {
    background: #f9fafb;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 20px;
    font-family: monospace;
}

.holerite-header {
    text-align: center;
    border-bottom: 1px solid #000;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.holerite-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    margin-bottom: 15px;
    border-bottom: 1px dashed #ccc;
    padding-bottom: 10px;
}

.holerite-table {
    width: 100%;
    font-size: 0.85rem;
    margin-bottom: 15px;
}

.holerite-table th {
    text-align: left;
    border-bottom: 1px solid #000;
    padding: 4px 0;
}

.holerite-table td {
    padding: 6px 0;
}

.holerite-totalizer {
    border-top: 1px solid #000;
    padding-top: 10px;
    font-size: 0.9rem;
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.holerite-liquido {
    background: #fff;
    border: 1px solid #000;
    padding: 10px;
    text-align: right;
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 15px;
}

.holerite-footer {
    border-top: 1px dashed #000;
    padding-top: 10px;
    font-size: 0.75rem;
    color: #4b5563;
    display: flex;
    justify-content: space-between;
}
</style>

<section class="page-header">
    <h2>Simular Nova Folha</h2>
    <a href="?id=folha" class="btn-primary" style="background: var(--muted); text-decoration: none;">Voltar</a>
</section>

<?php if ($erro): ?>
    <div style="background: #fef2f2; color: var(--danger); border: 1px solid #fca5a5; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
        <?= htmlspecialchars($erro) ?>
    </div>
<?php endif; ?>

<div class="folha-grid">
    <!-- Painel de entrada de dados -->
    <div class="panel-card">
        <h3 class="panel-title">Parâmetros de Lançamento</h3>
        
        <form action="?id=folha_gerar" method="POST">
            <div class="field-group">
                <label>Funcionário</label>
                <select name="funcionario_id" id="funcionario_id" required onchange="carregarDadosSalario()">
                    <option value="">Selecione o colaborador...</option>
                    <?php foreach ($funcionarios as $func): ?>
                        <option value="<?= htmlspecialchars($func['id']) ?>" data-salario="<?= htmlspecialchars($func['salario_base']) ?>">
                            <?= htmlspecialchars($func['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="field-group">
                    <label>Mês de Referência</label>
                    <select name="mes" id="mes" required onchange="atualizarPreviewMeta()">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= date('n') == $m ? 'selected' : '' ?>><?= sprintf('%02d', $m) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="field-group">
                    <label>Ano de Referência</label>
                    <select name="ano" id="ano" required onchange="atualizarPreviewMeta()">
                        <?php for ($y = date('Y') - 1; $y <= date('Y') + 2; $y++): ?>
                            <option value="<?= $y ?>" <?= date('Y') == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <h4 style="margin: 24px 0 12px; color: #4b5563; font-size: 0.95rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 4px;">Rubricas e Lançamentos</h4>

            <div class="form-row">
                <div class="field-group">
                    <label>Salário Bruto (R$)</label>
                    <input type="number" step="0.01" name="salario_bruto" id="salario_bruto" required oninput="calcularImpostos()">
                </div>
                <div class="field-group">
                    <!-- FGTS agora é totalmente editável -->
                    <label>Fundo de Garantia - FGTS (R$)</label>
                    <input type="number" step="0.01" name="valor_fgts" id="valor_fgts" required oninput="atualizarPreviewCompleto()">
                </div>
            </div>

            <div class="form-row">
                <div class="field-group">
                    <!-- INSS agora é editável -->
                    <label>Desconto INSS (R$)</label>
                    <input type="number" step="0.01" name="desconto_inss" id="desconto_inss" required oninput="atualizarPreviewCompleto()">
                </div>
                <div class="field-group">
                    <!-- IRPF agora é editável -->
                    <label>Desconto IRPF (R$)</label>
                    <input type="number" step="0.01" name="desconto_irpf" id="desconto_irpf" required oninput="atualizarPreviewCompleto()">
                </div>
            </div>

            <div class="field-group">
                <!-- Outros descontos não é mais obrigatório -->
                <label>Outros Descontos Adicionais (Opcional)</label>
                <input type="number" step="0.01" name="outros_descontos" id="outros_descontos" placeholder="0.00" oninput="atualizarPreviewCompleto()">
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; font-size: 1rem; margin-top: 10px;">Confirmar e Gerar Folha</button>
        </form>
    </div>

    <!-- Painel de espelho holerite interativo -->
    <div class="panel-card" style="align-self: start;">
        <h3 class="panel-title">Prévia do Holerite <span style="font-size: 0.75rem; background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 99px;">Atualização Automática</span></h3>
        
        <div class="holerite-preview">
            <div class="holerite-header">
                <strong>SISTEMA DE RH</strong><br>
                <span>Demonstrativo Interativo</span>
            </div>

            <div class="holerite-meta">
                <div>
                    <strong>Nome:</strong> <span id="prev_nome">Selecione um funcionário</span>
                </div>
                <div style="text-align: right;">
                    <strong>Competência:</strong> <span id="prev_competencia">--/----</span>
                </div>
            </div>

            <table class="holerite-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Descrição</th>
                        <th style="text-align: right; width: 25%;">Proventos</th>
                        <th style="text-align: right; width: 25%;">Descontos</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Salário Base</td>
                        <td style="text-align: right;" id="row_bruto">R$ 0,00</td>
                        <td style="text-align: right;">-</td>
                    </tr>
                    <tr>
                        <td>Previdência Social (INSS)</td>
                        <td style="text-align: right;">-</td>
                        <td style="text-align: right; color: #b91c1c;" id="row_inss">R$ 0,00</td>
                    </tr>
                    <tr>
                        <td>Imposto de Renda (IRPF)</td>
                        <td style="text-align: right;">-</td>
                        <td style="text-align: right; color: #b91c1c;" id="row_irpf">R$ 0,00</td>
                    </tr>
                    <tr id="row_outros_container" style="display: none;">
                        <td>Descontos Diversos</td>
                        <td style="text-align: right;">-</td>
                        <td style="text-align: right; color: #b91c1c;" id="row_outros">R$ 0,00</td>
                    </tr>
                </tbody>
            </table>

            <div class="holerite-totalizer">
                <div>Total Vencimentos: <span id="prev_vencimentos" style="font-weight: bold;">R$ 0,00</span></div>
                <div style="text-align: right;">Total Descontos: <span id="prev_descontos" style="font-weight: bold; color: #b91c1c;">R$ 0,00</span></div>
            </div>

            <div class="holerite-liquido">
                LÍQUIDO A RECEBER: <span id="prev_liquido">R$ 0,00</span>
            </div>

            <div class="holerite-footer">
                <div>Base FGTS: <span id="prev_base_fgts">R$ 0,00</span></div>
                <div style="text-align: right;">Depósito FGTS (8%): <span id="prev_valor_fgts">R$ 0,00</span></div>
            </div>
        </div>
    </div>
</div>

<script>
// Funções de Cálculo Progressivo
function calcularINSS(salario) {
    const teto = 8475.55;
    const s_calc = Math.min(salario, teto);
    let inss = 0;

    if (s_calc > 1621.00) inss += 1621.00 * 0.075;
    else return s_calc * 0.075;

    if (s_calc > 2902.84) inss += (2902.84 - 1621.00) * 0.09;
    else return inss + (s_calc - 1621.00) * 0.09;

    if (s_calc > 4354.27) inss += (4354.27 - 2902.84) * 0.12;
    else return inss + (s_calc - 2902.84) * 0.12;

    inss += (s_calc - 4354.27) * 0.14;
    return inss;
}

function calcularIRPF(salario, inss) {
    const base = salario - inss;
    if (base <= 2259.20) return 0;
    if (base <= 2826.65) return (base * 0.075) - 169.44;
    if (base <= 3751.05) return (base * 0.15) - 381.44;
    if (base <= 4664.68) return (base * 0.225) - 662.77;
    return (base * 0.275) - 896.00;
}

function carregarDadosSalario() {
    const select = document.getElementById('funcionario_id');
    const option = select.options[select.selectedIndex];
    const salario = option.getAttribute('data-salario');
    
    // Atualiza nome na prévia
    document.getElementById('prev_nome').innerText = option.text !== 'Selecione o colaborador...' ? option.text : 'Selecione um funcionário';

    if (salario) {
        document.getElementById('salario_bruto').value = parseFloat(salario).toFixed(2);
        calcularImpostos();
    } else {
        document.getElementById('salario_bruto').value = '';
        document.getElementById('desconto_inss').value = '';
        document.getElementById('desconto_irpf').value = '';
        document.getElementById('valor_fgts').value = '';
        atualizarPreviewCompleto();
    }
}

function calcularImpostos() {
    const bruto = parseFloat(document.getElementById('salario_bruto').value) || 0;
    
    // Calcula as estimativas de tributação recomendadas
    const inss = calcularINSS(bruto);
    const irpf = calcularIRPF(bruto, inss);
    const fgts = bruto * 0.08;

    // Prefila os inputs para edição manual livre
    document.getElementById('desconto_inss').value = inss.toFixed(2);
    document.getElementById('desconto_irpf').value = irpf.toFixed(2);
    document.getElementById('valor_fgts').value = fgts.toFixed(2);
    
    atualizarPreviewCompleto();
}

function formatarMoeda(valor) {
    return "R$ " + valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function atualizarPreviewMeta() {
    const mes = document.getElementById('mes').value.padStart(2, '0');
    const ano = document.getElementById('ano').value;
    document.getElementById('prev_competencia').innerText = mes + "/" + ano;
}

function atualizarPreviewCompleto() {
    atualizarPreviewMeta();

    const bruto = parseFloat(document.getElementById('salario_bruto').value) || 0;
    const inss = parseFloat(document.getElementById('desconto_inss').value) || 0;
    const irpf = parseFloat(document.getElementById('desconto_irpf').value) || 0;
    const fgts = parseFloat(document.getElementById('valor_fgts').value) || 0;
    const outros = parseFloat(document.getElementById('outros_descontos').value) || 0;

    const totalDescontos = inss + irpf + outros;
    const liquido = bruto - totalDescontos;

    // Atualiza elementos de texto do Holerite Interativo
    document.getElementById('row_bruto').innerText = formatarMoeda(bruto);
    document.getElementById('row_inss').innerText = formatarMoeda(inss);
    document.getElementById('row_irpf').innerText = formatarMoeda(irpf);

    // Gerencia o campo opcional de Outros Descontos no espelho
    const rowOutrosContainer = document.getElementById('row_outros_container');
    if (outros > 0) {
        rowOutrosContainer.style.display = 'table-row';
        document.getElementById('row_outros').innerText = formatarMoeda(outros);
    } else {
        rowOutrosContainer.style.display = 'none';
    }

    document.getElementById('prev_vencimentos').innerText = formatarMoeda(bruto);
    document.getElementById('prev_descontos').innerText = formatarMoeda(totalDescontos);
    document.getElementById('prev_liquido').innerText = formatarMoeda(liquido);

    // Rodapé de FGTS Informativo
    document.getElementById('prev_base_fgts').innerText = formatarMoeda(bruto);
    document.getElementById('prev_valor_fgts').innerText = formatarMoeda(fgts);
}

// Inicia com a competência carregada
atualizarPreviewMeta();
</script>