
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
    <title>PayFlow - Novo Cliente</title>
    <link rel="stylesheet" href="/payflow/public/css/style.css">
</head>

<body>

    <?php require_once __DIR__ . '/../layout/menu.php'; ?>

    <h1>PayFlow</h1>

    <h2>Cadastrar novo cliente</h2>

    <form action="/payflow/app/controllers/ClienteController.php" method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
        >

        <label>Nome completo:</label>
        <input type="text" name="nome" maxlength="150" required>

        <br><br>

        <label>CPF/CNPJ:</label>
        <input type="text" name="cpf_cnpj" required>

        <br><br>

        <label>E-mail:</label>
        <input type="email" name="email" required>

        <br><br>

        <label>Telefone:</label>
        <input type="text" name="telefone" required>

        <br><br>

        <button type="submit">Cadastrar cliente</button>

    </form>

    <br>

    <a href="/payflow/public/">Voltar para clientes</a>

</body>
</html>
