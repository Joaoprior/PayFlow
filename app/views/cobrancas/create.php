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
    <title>PayFlow - Nova Cobrança</title>
    <link rel="stylesheet" href="/payflow/public/css/style.css">
</head>

<body>

    <?php require_once __DIR__ . '/../layout/menu.php'; ?>

    <h1>PayFlow</h1>

    <h2>Nova cobrança</h2>

    <p>Preencha os dados para cadastrar uma cobrança.</p>

    <form action="/payflow/app/controllers/CobrancaController.php" method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
        >

        <label>Cliente:</label>
        <select name="cliente_id" required>
            <option value="">Selecione um cliente</option>

            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= (int) $cliente['id'] ?>">
                    <?= htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>

        </select>

        <label>Descrição:</label>
        <input type="text" name="descricao" maxlength="255" required>

        <label>Valor (R$):</label>
        <input type="number" name="valor" step="0.01" min="0.01" required>

        <label>Data de vencimento:</label>
        <input type="date" name="data_vencimento" required>

        <button type="submit">Cadastrar cobrança</button>

        <a href="/payflow/public/">Cancelar</a>

    </form>

</body>
</html>