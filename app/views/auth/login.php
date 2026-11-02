
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PayFlow - Login</title>

    <link rel="stylesheet" href="/payflow/public/css/style.css">
</head>

<body>

    <h1>PayFlow.</h1>

    <h2>Acessar sua conta</h2>

    <p>Entre para gerenciar suas cobranças.</p>

    <?php if (isset($_GET['erro'])): ?>
        <p style="color: #fca5a5;">
            E-mail ou senha inválidos.
        </p>
    <?php endif; ?>

    <form action="/payflow/app/controllers/LoginController.php" method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
        >

        <label for="email">E-mail</label>

        <input
            type="email"
            id="email"
            name="email"
            maxlength="254"
            placeholder="seuemail@exemplo.com"
            required
        >

        <label for="senha">Senha</label>

        <input
            type="password"
            id="senha"
            name="senha"
            placeholder="Digite sua senha"
            required
        >

        <button type="submit">Entrar</button>

    </form>

    <br>

    <p>
        Ainda não tem uma conta?
    </p>

    <a href="/payflow/public/?acao=cadastro">
        Criar conta
    </a>

</body>
</html>
