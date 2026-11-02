
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
    <title>PayFlow - Cobranças</title>
    <link rel="stylesheet" href="/payflow/public/css/style.css">
</head>

<body>

    <?php require_once __DIR__ . '/../layout/menu.php'; ?>

    <h1>PayFlow.</h1>
    <h2>Gerenciamento de cobranças</h2>

    <p>Consulte e acompanhe os pagamentos dos seus clientes.</p>


    <?php if (isset($_GET['boleto']) && $_GET['boleto'] === 'gerado'): ?>
        <p style="color: #86efac;">Boleto registrado com sucesso!</p>
    <?php endif; ?>

    <?php if (isset($_GET['boleto']) && $_GET['boleto'] === 'existente'): ?>
        <p style="color: #facc15;">
            Esta cobrança já possui um boleto cadastrado.
        </p>
    <?php endif; ?>

    <?php if (isset($_GET['erro'])): ?>
        <p style="color: #fca5a5;">
            Não foi possível executar a operação. Verifique a cobrança.
        </p>
    <?php endif; ?>

    <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'pagamento'): ?>
        <p style="color: #86efac;">Pagamento confirmado com sucesso!</p>
    <?php endif; ?>

    <?php if (isset($_GET['aviso']) && $_GET['aviso'] === 'ja_paga'): ?>
        <p style="color: #facc15;">Esta cobrança já estava paga.</p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Vencimento</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>

            <?php if (empty($cobrancas)): ?>
                <tr>
                    <td colspan="7">Nenhuma cobrança cadastrada.</td>
                </tr>
            <?php else: ?>

                <?php foreach ($cobrancas as $cobranca): ?>
                    <tr>
                        <td><?= (int) $cobranca['id'] ?></td>

                        <td>
                            <?= htmlspecialchars($cobranca['nome_cliente'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($cobranca['descricao'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            R$ <?= number_format($cobranca['valor'], 2, ',', '.') ?>
                        </td>

                        <td>
                            <?= date('d/m/Y', strtotime($cobranca['data_vencimento'])) ?>
                        </td>

                        <td>
                            <?php if ($cobranca['status_atual'] === 'Pago'): ?>
                                <span class="status status-paid">Pago</span>

                            <?php elseif ($cobranca['status_atual'] === 'Vencido'): ?>
                                <span class="status status-overdue">Vencido</span>

                            <?php else: ?>
                                <span class="status status-pending">Pendente</span>
                            <?php endif; ?>
                        </td>

                        <td>

                            <?php if (!empty($cobranca['boleto'])): ?>

                                <a class="action-link" href="/payflow/public/?acao=ver_boleto&id=<?= (int) $cobranca['id'] ?>">
                                    Consultar boleto
                                </a>

                            <?php elseif ($cobranca['status_atual'] !== 'Pago'): ?>

                                <form
                                    action="/payflow/app/controllers/GerarBoletoController.php"
                                    method="POST"
                                    class="action-form"
                                >
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                                    <input
                                        type="hidden"
                                        name="cobranca_id"
                                        value="<?= (int) $cobranca['id'] ?>"
                                    >

                                    <button type="submit" class="button-small">
                                        Gerar boleto
                                    </button>
                                </form>

                            <?php endif; ?>

                            <?php if ($cobranca['status_atual'] !== 'Pago'): ?>

                                <form
                                    action="/payflow/app/controllers/PagarCobrancaController.php"
                                    method="POST"
                                    class="action-form"
                                    onsubmit="return confirm('Confirma que esta cobrança foi paga?');"
                                >
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int) $cobranca['id'] ?>"
                                    >

                                    <button type="submit" class="button-small">
                                        Marcar como paga
                                    </button>
                                </form>

                            <?php else: ?>

                                <span class="payment-confirmed">
                                    Pagamento confirmado
                                </span>

                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>
    </table>

</body>
</html>
