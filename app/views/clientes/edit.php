
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
    <title>PayFlow - Editar Cliente</title>

    <link rel="stylesheet" href="/payflow/public/css/style.css">
</head>

<body>

    <?php require_once __DIR__ . '/../layout/menu.php'; ?>

    <h1>PayFlow</h1>

    <h2>Editar cliente</h2>

    <p>Atualize os dados do cliente cadastrado.</p>

    <form action="/payflow/app/controllers/ClienteController.php" method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
        >

        <input
            type="hidden"
            name="id"
            value="<?= (int) $cliente['id'] ?>"
        >

        <label>Nome completo:</label>
        <input
            type="text"
            name="nome"
            maxlength="150"
            value="<?= htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label>CPF/CNPJ:</label>
        <input
            type="text"
            name="cpf_cnpj"
            value="<?= htmlspecialchars($cliente['cpf_cnpj'], ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label>E-mail:</label>
        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($cliente['email'], ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label>Telefone:</label>
        <input
            type="text"
            name="telefone"
            value="<?= htmlspecialchars($cliente['telefone'], ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <button type="submit">Salvar alterações</button>

        <a href="/payflow/public/">Cancelar</a>

    </form>

</body>
</html>
