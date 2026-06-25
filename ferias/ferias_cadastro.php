<?php
require_once './config/conexao.php';
$erro = '';

// Busca todos os funcionários ativos e seus salários base
$resultado_func = $conn->query("SELECT id, nome, salario_base FROM funcionarios WHERE status != 'Inativo' ORDER BY nome ASC");
$funcionarios = $resultado_func ? $resultado_func->fetch_all(MYSQLI_ASSOC) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $funcionario_id = $_POST['funcionario_id'] ?? '';
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';
    $valor_bruto = floatval($_POST['valor_bruto'] ?? 0);
    $desconto_inss = floatval($_POST['desconto_inss'] ?? 0);
    $desconto_irpf = floatval($_POST['desconto_irpf'] ?? 0);
    $status = $_POST['status'] ?? 'Agendada';

    if (!empty($funcionario_id) && !empty($data_inicio) && !empty($data_fim)) {
        if (strtotime($data_inicio) > strtotime($data_fim)) {
            $erro = "A data de início não pode ser maior do que a data de término das férias.";
        } else {
            $stmt = $conn->prepare("INSERT INTO ferias (funcionario_id, data_inicio, data_fim, valor_bruto, desconto_inss, desconto_irpf, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issddds", $funcionario_id, $data_inicio, $data_fim, $valor_bruto, $desconto_inss, $desconto_irpf, $status);

            if ($stmt->execute()) {
                header("Location: index.php?id=ferias");
                exit;
            } else {
                $erro = "Erro ao registrar férias: " . $conn->error;
            }
            $stmt->close();
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}
?>

<style>
.ferias-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    margin-top: 20px;
}

@media (min-width: 992px) {
    .ferias-grid {
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

/* Espelho do Recibo de Férias Interativo */
.recibo-preview {
    background: #f9fafb;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 20px;
    font-family: monospace;
}

.recibo-header {
    text-align: center;
    border-bottom: 1px solid #000;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.recibo-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    margin-bottom: 15px;
    border-bottom: 1px dashed #ccc;
    padding-bottom: 10px;
    line-height: 1.4;
}

.recibo-table {
    width: 100%;
    font-size: 0.85rem;
    margin-bottom: 15px;
}

.recibo-table th {
    text-align: left;
    border-bottom: 1px solid #000;
    padding: 4px 0;
}

.recibo-table td {
    padding: 6px 0;
}

.recibo-totalizer {
    border-top: 1px solid #000;
    padding-top: 10px;
    font-size: 0.9rem;
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.recibo-liquido {
    background: #fff;
    border: 1px solid #000;
    padding: 10px;
    text-align: right;
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 15px;
}

.recibo-footer {
    border-top: 1px dashed #000;
    padding-top: 10px;
    font-size: 0.75rem;
    color: #4b5563;
    display: flex;
    justify-content: space-between;
}
</style>

<div class="ferias-grid">
    <!-- Entrada de Parâmetros -->
    <div class="panel-card">
        <h3 class="panel-title">Parâmetros de Concessão de Férias</h3>
        
        <form action="?id=ferias_cadastro" method="POST">
            <div class="field-group">
                <label>Funcionário</label>
                <select name="funcionario_id" id="funcionario_id" required onchange="carregarDadosFuncionario()">
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
                    <label>Data Início</label>
                    <input type="date" name="data_inicio" id="data_inicio" required onchange="calcularPeriodoFeria()">
                </div>
                <div class="field-group">
                    <label>Data Fim</label>
                    <input type="date" name="data_fim" id="data_fim" required onchange="calcularPeriodoFeria()">
                </div>
            </div>

            <h4 style="margin: 24px 0 12px; color: #4b5563; font-size: 0.95rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 4px;">Valores e Retenções de Férias</h4>

            <div class="form-row">
                <div class="field-group">
                    <label>Total Bruto de Férias (Férias + 1/3) (R$)</label>
                    <input type="number" step="0.01" name="valor_bruto" id="valor_bruto" required oninput="calcularLiquido()">
                </div>
                <div class="field-group">
                    <label>Status</label>
                    <select name="status" id="status" onchange="atualizarPreviewCompleto()">
                        <option value="Agendada">Agendada</option>
                        <option value="Em andamento">Em andamento</option>
                        <option value="Concluida">Concluída</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="field-group">
                    <!-- INSS de Férias Editável -->
                    <label>Desconto INSS s/ Férias (R$)</label>
                    <input type="number" step="0.01" name="desconto_inss" id="desconto_inss" required oninput="calcularLiquido()">
                </div>
                <div class="field-group">
                    <!-- IRPF de Férias Editável -->
                    <label>Desconto IRPF s/ Férias (R$)</label>
                    <input type="number" step="0.01" name="desconto_irpf" id="desconto_irpf" required oninput="calcularLiquido()">
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; font-size: 1rem; margin-top: 10px;">Salvar Agendamento</button>
        </form>
    </div>

    <!-- Espelho Interativo de Recibo de Férias -->
    <div class="panel-card" style="align-self: start;">
        <h3 class="panel-title">Prévia do Recibo de Férias <span style="font-size: 0.75rem; background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 99px;">Tempo Real</span></h3>
        
        <div class="recibo-preview">
            <div class="recibo-header">
                <strong>SISTEMA DE RH</strong><br>
                <span>Aviso e Recibo de Férias</span>
            </div>

            <div class="recibo-meta">
                <div>
                    <strong>Nome:</strong> <span id="prev_nome">Selecione o funcionário</span><br>
                    <strong>Período:</strong> <span id="prev_periodo">--/--/---- a --/--/----</span>
                </div>
                <div style="text-align: right;">
                    <strong>Dias de Gozo:</strong> <span id="prev_dias">0 dias</span><br>
                    <strong>Status:</strong> <span id="prev_status">Agendada</span>
                </div>
            </div>

            <table class="recibo-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Descrição</th>
                        <th style="text-align: right; width: 25%;">Vencimentos</th>
                        <th style="text-align: right; width: 25%;">Descontos</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Férias Proporcionais</td>
                        <td style="text-align: right;" id="row_prop">R$ 0,00</td>
                        <td style="text-align: right;">-</td>
                    </tr>
                    <tr>
                        <td>1/3 Constitucional de Férias</td>
                        <td style="text-align: right;" id="row_terco">R$ 0,00</td>
                        <td style="text-align: right;">-</td>
                    </tr>
                    <tr>
                        <td>INSS s/ Férias</td>
                        <td style="text-align: right;">-</td>
                        <td style="text-align: right; color: #b91c1c;" id="row_inss">R$ 0,00</td>
                    </tr>
                    <tr>
                        <td>IRPF s/ Férias</td>
                        <td style="text-align: right;">-</td>
                        <td style="text-align: right; color: #b91c1c;" id="row_irpf">R$ 0,00</td>
                    </tr>
                </tbody>
            </table>

            <div class="recibo-totalizer">
                <div>Total Bruto: <span id="prev_bruto" style="font-weight: bold;">R$ 0,00</span></div>
                <div style="text-align: right;">Retenções: <span id="prev_descontos" style="font-weight: bold; color: #b91c1c;">R$ 0,00</span></div>
            </div>

            <div class="recibo-liquido">
                LÍQUIDO DE FÉRIAS: <span id="prev_liquido">R$ 0,00</span>
            </div>

            <div class="recibo-footer">
                <div>Base de Cálculo: <span id="prev_base">R$ 0,00</span></div>
                <div>Emissão Aviso: <span id="prev_aviso">--/--/----</span></div>
            </div>
        </div>
    </div>
</div>

<script>
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

function carregarDadosFuncionario() {
    const select = document.getElementById('funcionario_id');
    const option = select.options[select.selectedIndex];
    document.getElementById('prev_nome').innerText = option.text !== 'Selecione o colaborador...' ? option.text : 'Selecione o funcionário';
    calcularPeriodoFeria();
}

function calcularPeriodoFeria() {
    const startVal = document.getElementById('data_inicio').value;
    const endVal = document.getElementById('data_fim').value;
    const select = document.getElementById('funcionario_id');
    const option = select.options[select.selectedIndex];
    const salario = parseFloat(option.getAttribute('data-salario')) || 0;

    if (startVal && endVal && salario > 0) {
        // Correção de fuso horário no Javascript ao instanciar datas
        const start = new Date(startVal + 'T00:00:00');
        const end = new Date(endVal + 'T00:00:00');

        if (start <= end) {
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            document.getElementById('prev_dias').innerText = diffDays + " dias";
            document.getElementById('prev_periodo').innerText = start.toLocaleDateString('pt-BR') + " a " + end.toLocaleDateString('pt-BR');
            
            // Datas do Aviso (30 dias antes)
            const avisoDate = new Date(start);
            avisoDate.setDate(avisoDate.getDate() - 30);
            document.getElementById('prev_aviso').innerText = avisoDate.toLocaleDateString('pt-BR');

            // Cálculos CLT
            const valorDiario = salario / 30;
            const proporcional = valorDiario * diffDays;
            const terco = proporcional / 3;
            const bruto = proporcional + terco;

            // Auto-calcula INSS e IRPF baseados no bruto das férias
            const inss = calcularINSS(bruto);
            const irpf = calcularIRPF(bruto, inss);

            // Preenche os campos editáveis
            document.getElementById('valor_bruto').value = bruto.toFixed(2);
            document.getElementById('desconto_inss').value = inss.toFixed(2);
            document.getElementById('desconto_irpf').value = irpf.toFixed(2);

            // Armazena variáveis para exibição
            document.getElementById('row_prop').innerText = formatarMoeda(proporcional);
            document.getElementById('row_terco').innerText = formatarMoeda(terco);
            document.getElementById('prev_base').innerText = formatarMoeda(salario);
        }
    }
    atualizarPreviewCompleto();
}

function formatarMoeda(valor) {
    return "R$ " + valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function atualizarPreviewCompleto() {
    const bruto = parseFloat(document.getElementById('valor_bruto').value) || 0;
    const inss = parseFloat(document.getElementById('desconto_inss').value) || 0;
    const irpf = parseFloat(document.getElementById('desconto_irpf').value) || 0;
    const statusSelect = document.getElementById('status');
    const statusText = statusSelect.options[statusSelect.selectedIndex].text;

    const totalDescontos = inss + irpf;
    const liquido = bruto - totalDescontos;

    // Atualização em tempo real do holerite de férias
    document.getElementById('row_inss').innerText = formatarMoeda(inss);
    document.getElementById('row_irpf').innerText = formatarMoeda(irpf);
    document.getElementById('prev_bruto').innerText = formatarMoeda(bruto);
    document.getElementById('prev_descontos').innerText = formatarMoeda(totalDescontos);
    document.getElementById('prev_liquido').innerText = formatarMoeda(liquido);
    document.getElementById('prev_status').innerText = statusText;
}
</script>