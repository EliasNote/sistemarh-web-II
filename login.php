<?php
session_start();

$admin = [
    'id' => 1,
    'nome' => 'admin',
    'senha' => 'admin',
];

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if ($usuario === $admin['nome'] && $senha === $admin['senha']) {
        $_SESSION['logado'] = true;
        $_SESSION['usuario_id'] = $admin['id'];
        header('Location: index.php');
        exit;
    } else {
        $erro = 'Usuário ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema RH</title>
    <link rel="stylesheet" href="login.css">
</head>
<body class="login-body">
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h1>Sistema RH</h1>
                <p>Faça login para acessar o painel</p>
            </div>

            <?php if ($erro): ?>
                <div class="alert-danger">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="usuario">Usuário</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Digite seu usuário" required autofocus>
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
                </div>

                <button type="submit" class="btn-primary btn-block">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>