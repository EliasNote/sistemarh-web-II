<?php
require_once './config/conexao.php';
$erro = '';
$registro_id = $_GET['registro'] ?? '';

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
            $stmt = $conn->prepare("UPDATE ferias SET funcionario_id=?, data_inicio=?, data_fim=?, valor_bruto=?, desconto_inss=?, desconto_irpf=?, status=? WHERE id=?");
            $stmt->bind_param("issdddsi", $funcionario_id, $data_inicio, $data_fim, $valor_bruto, $desconto_inss, $desconto_irpf, $status, $registro_id);

            if ($stmt->execute()) {
                header("Location: index.php?id=ferias");
                exit;
            } else {
                $erro = "Erro ao atualizar: " . $conn->error;
            }
            $stmt->close();
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}

$stmt = $conn->prepare("SELECT * FROM ferias WHERE id = ?");
$stmt->bind_param("i", $registro_id);
$stmt->execute();
$dados_ferias = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$dados_ferias) {
    header("Location: index.php?id=ferias");
    exit;
}
?>

<section class="page-header">
    <h2>Editar Férias</h2>
    <a href="?id=ferias" class="btn-primary btn-primary--muted btn-primary--link">Voltar</a>
</section>

<?php if ($erro): ?>
    <div class="alert-error">
        <?= htmlspecialchars($erro) ?>
    </div>
<?php endif; ?>

<div class="ferias-grid">
    <div class="panel-card">
        <h3 class="panel-title">Parâmetros de Concessão</h3>
        
        <form action="?id=ferias_editar&registro=<?= $registro_id ?>" method="POST">
            <div class="field-group">
                <label class="form-label">Funcionário</label>
                <select name="funcionario_id" id="funcionario_id" required onchange="carregarDadosFuncionario()" class="form-control">
                    <?php foreach ($funcionarios as $func): ?>
                        <option value="<?= htmlspecialchars($func['id']) ?>" data-salario="<?= htmlspecialchars($func['salario_base']) ?>" <?= $dados_ferias['funcionario_id'] == $func['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($func['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="field-group">
                    <label class="form-label">Data Início</label>
                    <input type="date" name="data_inicio" id="data_inicio" value="<?= htmlspecialchars($dados_ferias['data_inicio']) ?>" required onchange="calcularPeriodoFeria()" class="form-control">
                </div>
                <div class="field-group">
                    <label class="form-label">Data Fim</label>
                    <input type="date" name="data_fim" id="data_fim" value="<?= htmlspecialchars($dados_ferias['data_fim']) ?>" required onchange="calcularPeriodoFeria()" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="field-group">
                    <label class="form-label">Total Bruto (Férias + 1/3) (R$)</label>
                    <input type="number" step="0.01" name="valor_bruto" id="valor_bruto" value="<?= htmlspecialchars($dados_ferias['valor_bruto']) ?>" required oninput="calcularLiquido()" class="form-control">
                </div>
                <div class="field-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="status" onchange="atualizarPreviewCompleto()" class="form-control">
                        <option value="Agendada" <?= $dados_ferias['status'] === 'Agendada' ? 'selected' : '' ?>>Agendada</option>
                        <option value="Em andamento" <?= $dados_ferias['status'] === 'Em andamento' ? 'selected' : '' ?>>Em andamento</option>
                        <option value="Concluida" <?= $dados_ferias['status'] === 'Concluida' ? 'selected' : '' ?>>Concluída</option>
                        <option value="Cancelada" <?= $dados_ferias['status'] === 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="field-group">
                    <label class="form-label">Desconto INSS s/ Férias (R$)</label>
                    <input type="number" step="0.01" name="desconto_inss" id="desconto_inss" value="<?= htmlspecialchars($dados_ferias['desconto_inss']) ?>" required oninput="calcularLiquido()" class="form-control">
                </div>
                <div class="field-group">
                    <label class="form-label">Desconto IRPF s/ Férias (R$)</label>
                    <input type="number" step="0.01" name="desconto_irpf" id="desconto_irpf" value="<?= htmlspecialchars($dados_ferias['desconto_irpf']) ?>" required oninput="calcularLiquido()" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn-primary ferias-submit">Salvar Alterações</button>
        </form>
    </div>

    <!-- Espelho de Prévia do Holerite de Férias -->
    <div class="panel-card panel-card--preview">
        <h3 class="panel-title">Prévia das Férias</h3>
        
        <div class="recibo-preview">
            <div class="recibo-header">
                <strong>SISTEMA DE RH</strong><br>
                <span>Aviso e Recibo de Férias</span>
            </div>

            <div class="recibo-meta">
                <div>
                    <strong>Nome:</strong> <span id="prev_nome">--</span><br>
                    <strong>Período:</strong> <span id="prev_periodo">--</span>
                </div>
                <div class="text-right">
                    <strong>Dias:</strong> <span id="prev_dias">0 dias</span><br>
                    <strong>Status:</strong> <span id="prev_status">Agendada</span>
                </div>
            </div>

            <table class="recibo-table">
                <thead>
                    <tr>
                        <th class="col-description">Descrição</th>
                        <th class="col-money">Vencimentos</th>
                        <th class="col-money">Descontos</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Férias Proporcionais</td>
                        <td class="text-right" id="row_prop">R$ 0,00</td>
                        <td class="text-right">-</td>
                    </tr>
                    <tr>
                        <td>1/3 Constitucional de Férias</td>
                        <td class="text-right" id="row_terco">R$ 0,00</td>
                        <td class="text-right">-</td>
                    </tr>
                    <tr>
                        <td>INSS s/ Férias</td>
                        <td class="text-right">-</td>
                        <td class="text-right text-danger" id="row_inss">R$ 0,00</td>
                    </tr>
                    <tr>
                        <td>IRPF s/ Férias</td>
                        <td class="text-right">-</td>
                        <td class="text-right text-danger" id="row_irpf">R$ 0,00</td>
                    </tr>
                </tbody>
            </table>

            <div class="recibo-totalizer">
                <div>Total Bruto: <span id="prev_bruto" class="font-bold">R$ 0,00</span></div>
                <div class="text-right">Retenções: <span id="prev_descontos" class="font-bold text-danger">R$ 0,00</span></div>
            </div>

            <div class="recibo-liquido">
                LÍQUIDO DE FÉRIAS: <span id="prev_liquido">R$ 0,00</span>
            </div>

            <div class="recibo-footer">
                <div>Base: <span id="prev_base">R$ 0,00</span></div>
                <div class="text-right">Aviso: <span id="prev_aviso">--</span></div>
            </div>
        </div>
    </div>
</div>

<script>
// Mantém as mesmas funções de cálculo JS do arquivo anterior para atualização em tempo real
function calcularPeriodoFeria() {
    const startVal = document.getElementById('data_inicio').value;
    const endVal = document.getElementById('data_fim').value;
    const select = document.getElementById('funcionario_id');
    const option = select.options[select.selectedIndex];
    const salario = parseFloat(option.getAttribute('data-salario')) || 0;

    if (startVal && endVal && salario > 0) {
        const start = new Date(startVal + 'T00:00:00');
        const end = new Date(endVal + 'T00:00:00');

        if (start <= end) {
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            document.getElementById('prev_dias').innerText = diffDays + " dias";
            document.getElementById('prev_periodo').innerText = start.toLocaleDateString('pt-BR') + " a " + end.toLocaleDateString('pt-BR');
            
            const avisoDate = new Date(start);
            avisoDate.setDate(avisoDate.getDate() - 30);
            document.getElementById('prev_aviso').innerText = avisoDate.toLocaleDateString('pt-BR');

            const valorDiario = salario / 30;
            const proporcional = valorDiario * diffDays;
            const terco = proporcional / 3;

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

    document.getElementById('row_inss').innerText = formatarMoeda(inss);
    document.getElementById('row_irpf').innerText = formatarMoeda(irpf);
    document.getElementById('prev_bruto').innerText = formatarMoeda(bruto);
    document.getElementById('prev_descontos').innerText = formatarMoeda(totalDescontos);
    document.getElementById('prev_liquido').innerText = formatarMoeda(liquido);
    document.getElementById('prev_status').innerText = statusText;
}

// Inicializa a tela com o nome e as datas carregadas do registro existente
window.onload = function() {
    const select = document.getElementById('funcionario_id');
    document.getElementById('prev_nome').innerText = select.options[select.selectedIndex].text;
    calcularPeriodoFeria();
}
</script>