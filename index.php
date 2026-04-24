<?php

$id = $_GET['id'] ?? '';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema RH - Funcionários</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <a href="/" class="sidebar__brand" style="text-decoration: none; color: inherit;">
                <h1 style="margin: 0;">Sistema RH</h1>
                <p style="margin: 4px 0 20px;">Painel Administrativo</p>
            </a>

            <nav class="sidebar__nav" aria-label="Menu principal">
                <a href="?id=funcionarios" class="sidebar__link <?= $id == 'funcionarios' ? 'active' : '' ?>">Funcionários</a>
                <a href="?id=cargos" class="sidebar__link <?= $id == 'cargos' ? 'active' : '' ?>">Cargos</a>
                <a href="?id=ferias" class="sidebar__link <?= $id == 'ferias' ? 'active' : '' ?>">Férias</a>
                <a href="?id=folha" class="sidebar__link <?= $id == 'folha' ? 'active' : '' ?>">Folha (Simulação)</a>
                <a href="/login.php" class="sidebar__link <?= $id == 'sair' ? 'active' : '' ?>">Sair</a>
            </nav>
        </aside>

        <main class="content">
            <?php
                if ($id === 'funcionarios') include 'funcionarios.php';
            ?>
        </main>
    </div>
</body>
</html>
