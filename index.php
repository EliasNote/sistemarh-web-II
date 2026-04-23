<?php

$aba_atual = "funcionarios";

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
            <div class="sidebar__brand">
                <h1>Sistema RH</h1>
                <p>Painel Administrativo</p>
            </div>

            <nav class="sidebar__nav" aria-label="Menu principal">
                <a href="#" class="sidebar__link active">Funcionários</a>
                <a href="#" class="sidebar__link">Cargos</a>
                <a href="#" class="sidebar__link">Férias</a>
                <a href="#" class="sidebar__link">Folha (Simulação)</a>
                <a href="#" class="sidebar__link">Sair</a>
            </nav>
        </aside>

        <main class="content">
            <?php include 'funcionarios.php'?>
        </main>
    </div>
</body>
</html>
