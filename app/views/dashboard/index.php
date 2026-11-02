
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = htmlspecialchars(
    $_SESSION['csrf_token'],
    ENT_QUOTES,
    'UTF-8'
);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PayFlow - Painel Financeiro</title>

    <link rel="stylesheet" href="/payflow/public/css/style.css">
</head>

<body>

    <?php require_once __DIR__ . '/../layout/menu.php'; ?>

    <header class="dashboard-header">

        <h1>PayFlow.</h1>

        <h2>Painel financeiro</h2>

        <p>
            Olá,
            <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8') ?>!
            Acompanhe a situação financeira das suas cobranças.
        </p>

    </header>


    <main>

        <div class="dashboard-grid">

            <section class="metric-card received">
                <div class="metric-label">Total recebido</div>

                <div class="metric-value">
                    R$ <?= number_format($resumo['total_recebido'], 2, ',', '.') ?>
                </div>

                <div class="metric-note">Cobranças pagas</div>
            </section>

            <section class="metric-card pending">
                <div class="metric-label">Total pendente</div>

                <div class="metric-value">
                    R$ <?= number_format($resumo['total_pendente'], 2, ',', '.') ?>
                </div>

                <div class="metric-note">Cobranças dentro do prazo</div>
            </section>

            <section class="metric-card overdue">
                <div class="metric-label">Total vencido</div>

                <div class="metric-value">
                    R$ <?= number_format($resumo['total_vencido'], 2, ',', '.') ?>
                </div>

                <div class="metric-note">Cobranças atrasadas</div>
            </section>

        </div>

    </main>

    <footer class="dashboard-footer">
        PayFlow — Sistema de gestão de cobranças.
    </footer>

</body>
</html>
