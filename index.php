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
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <a href="index.php" class="sidebar__brand" style="text-decoration: none; color: inherit;">
                <h1 style="margin: 0;">Sistema RH</h1>
                <p style="margin: 4px 0 20px;">Painel Administrativo</p>
            </a>

            <nav class="sidebar__nav" aria-label="Menu principal">
                <a href="?id=funcionarios" class="sidebar__link <?= in_array($id, ['funcionarios', 'funcionario_cadastro', 'funcionario_editar']) ? 'active' : '' ?>">Funcionários</a>
                <a href="?id=cargos" class="sidebar__link <?= in_array($id, ['cargos', 'cargo_cadastro', 'cargo_editar']) ? 'active' : '' ?>">Cargos</a>
                <a href="?id=ferias" class="sidebar__link <?= in_array($id, ['ferias', 'ferias_cadastro', 'ferias_editar', 'ferias_detalhes']) ? 'active' : '' ?>">Férias</a>
                <a href="?id=folha" class="sidebar__link <?= in_array($id, ['folha', 'folha_gerar', 'folha_detalhes']) ? 'active' : '' ?>">Folha (Simulação)</a>
                <a href="login.php" class="sidebar__link">Sair</a>
            </nav>
        </aside>

        <main class="content">
            <?php
                if ($id === 'funcionarios') include 'funcionarios.php';
                elseif ($id === 'funcionario_cadastro') include 'funcionario_cadastro.php';
                elseif ($id === 'funcionario_editar') include 'funcionario_editar.php';
                elseif ($id === 'funcionario_excluir') include 'funcionario_excluir.php';
                
                elseif ($id === 'cargos') include 'cargos.php';
                elseif ($id === 'cargo_cadastro') include 'cargo_cadastro.php';
                elseif ($id === 'cargo_editar') include 'cargo_editar.php';
                elseif ($id === 'cargo_excluir') include 'cargo_excluir.php';

                elseif ($id === 'ferias') include 'ferias.php';
                elseif ($id === 'ferias_cadastro') include 'ferias_cadastro.php';
                elseif ($id === 'ferias_editar') include 'ferias_editar.php';
                elseif ($id === 'ferias_excluir') include 'ferias_excluir.php';
                elseif ($id === 'ferias_detalhes') include 'ferias_detalhes.php';

                elseif ($id === 'folha') include 'folha.php';
                elseif ($id === 'folha_gerar') include 'folha_gerar.php';
                elseif ($id === 'folha_detalhes') include 'folha_detalhes.php';
                elseif ($id === 'folha_excluir') include 'folha_excluir.php';
            ?>
        </main>
    </div>
</body>
</html>