<?php
ob_start();
$id = $_GET['id'] ?? 'funcionarios';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema RH - Painel</title>
    <link rel="stylesheet" href="styles.css">
    <?php if (in_array($id, ['funcionarios', 'funcionario_cadastro', 'funcionario_editar'])): ?>
        <link rel="stylesheet" href="funcionario/funcionario.css">
    <?php endif; ?>
    <?php if (in_array($id, ['folha', 'folha_gerar', 'folha_detalhes'])): ?>
        <link rel="stylesheet" href="folha/folha.css">
    <?php endif; ?>
    <?php if (in_array($id, ['ferias', 'ferias_cadastro', 'ferias_editar', 'ferias_detalhes'])): ?>
        <link rel="stylesheet" href="ferias/ferias.css">
    <?php endif; ?>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <a href="index.php" class="sidebar__brand">
                <h1>Sistema RH</h1>
                <p>Painel Administrativo</p>
            </a>

            <nav class="sidebar__nav" aria-label="Menu principal">
                <a href="?id=funcionarios" class="sidebar__link <?= in_array($id, ['funcionarios', 'funcionario_cadastro', 'funcionario_editar']) ? 'active' : '' ?>">Funcionários</a>
                <a href="?id=cargos" class="sidebar__link <?= in_array($id, ['cargos', 'cargo_cadastro', 'cargo_editar']) ? 'active' : '' ?>">Cargos</a>
                <a href="?id=ferias" class="sidebar__link <?= in_array($id, ['ferias', 'ferias_cadastro', 'ferias_editar', 'ferias_detalhes']) ? 'active' : '' ?>">Férias</a>
                <a href="?id=folha" class="sidebar__link <?= in_array($id, ['folha', 'folha_gerar', 'folha_detalhes']) ? 'active' : '' ?>">Folha (Simulação)</a>
                <a href="/login/" class="sidebar__link">Sair</a>
            </nav>
        </aside>

        <main class="content">
            <?php
                if ($id === 'funcionarios') include './funcionario/index.php';
                elseif ($id === 'funcionario_cadastro') include './funcionario/funcionario_cadastro.php';
                elseif ($id === 'funcionario_editar') include './funcionario/funcionario_editar.php';
                elseif ($id === 'funcionario_excluir') include './funcionario/funcionario_excluir.php';
                
                elseif ($id === 'cargos') include './cargo/index.php';
                elseif ($id === 'cargo_cadastro') include './cargo/cargo_cadastro.php';
                elseif ($id === 'cargo_editar') include './cargo/cargo_editar.php';
                elseif ($id === 'cargo_excluir') include './cargo/cargo_excluir.php';

                elseif ($id === 'ferias') include './ferias/index.php';
                elseif ($id === 'ferias_cadastro') include './ferias/ferias_cadastro.php';
                elseif ($id === 'ferias_editar') include './ferias/ferias_editar.php';
                elseif ($id === 'ferias_excluir') include './ferias/ferias_excluir.php';
                elseif ($id === 'ferias_detalhes') include './ferias/ferias_detalhes.php';

                elseif ($id === 'folha') include './folha/index.php';
                elseif ($id === 'folha_gerar') include './folha/folha_gerar.php';
                elseif ($id === 'folha_detalhes') include './folha/folha_detalhes.php';
                elseif ($id === 'folha_excluir') include './folha/folha_excluir.php';
            ?>
        </main>
    </div>
</body>
</html>