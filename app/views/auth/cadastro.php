
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

    <title>PayFlow - Criar conta</title>

    <link rel="stylesheet" href="/payflow/public/css/style.css">
</head>

<body>

    <h1>PayFlow.</h1>

    <h2>Criar sua conta</h2>

    <p>Cadastre-se para começar a gerenciar suas cobranças.</p>

    <?php if (isset($_GET['sucesso'])): ?>
        <p style="color: #86efac;">
            Conta criada com sucesso! Agora você pode fazer login.
        </p>
    <?php endif; ?>

    <?php if (isset($_GET['erro'])): ?>

        <?php
        $mensagens = [
            'campos' => 'Preencha todos os campos.',
            'nome' => 'O nome informado é muito longo.',
            'email' => 'Informe um e-mail válido.',
            'senha' => 'A senha precisa ter pelo menos 8 caracteres.',
            'confirmacao' => 'As senhas não são iguais.',
            'duplicado' => 'Este e-mail já está cadastrado.',
            'cadastro' => 'Não foi possível criar sua conta.'
        ];

        $erro = $_GET['erro'];
        ?>

        <p style="color: #fca5a5;">
            <?= htmlspecialchars($mensagens[$erro] ?? 'Ocorreu um erro.', ENT_QUOTES, 'UTF-8') ?>
        </p>

    <?php endif; ?>

    <form action="/payflow/app/controllers/CadastroUsuarioController.php" method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
        >

        <label for="nome">Nome completo</label>
        <input
            type="text"
            id="nome"
            name="nome"
            maxlength="150"
            placeholder="Digite seu nome"
            required
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
            placeholder="Mínimo de 8 caracteres"
            minlength="8"
            required
        >

        <label for="confirmar_senha">Confirmar senha</label>
        <input
            type="password"
            id="confirmar_senha"
            name="confirmar_senha"
            placeholder="Digite a senha novamente"
            minlength="8"
            required
        >

        <button type="submit">Criar conta</button>

    </form>

    <br>

    <a href="/payflow/public/?acao=login">Já possui uma conta? Entrar</a>

</body>
</html>
