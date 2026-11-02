<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$menuCsrf = htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');
?>
<nav class="dashboard-nav payflow-menu" aria-label="Menu principal">
    <a href="/payflow/public/?acao=dashboard">Início</a>
    <a href="/payflow/public/">Clientes</a>
    <a href="/payflow/public/?acao=cobrancas">Cobranças</a>
    <a href="/payflow/public/?acao=nova_cobranca">Nova cobrança</a>
    <form action="/payflow/app/controllers/LogoutController.php" method="POST" class="menu-logout">
        <input type="hidden" name="csrf_token" value="<?= $menuCsrf ?>">
        <button type="submit">Sair</button>
    </form>
</nav>
