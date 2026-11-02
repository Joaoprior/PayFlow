
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
    <title>PayFlow - Clientes</title>
    <link rel="stylesheet" href="/payflow/public/css/style.css">
</head>

<body>

    <?php require_once __DIR__ . '/../layout/menu.php'; ?>

    <h1>PayFlow</h1>

    <h2>Clientes</h2>

    <p>Gerencie os clientes cadastrados no sistema.</p>

    <?php if (isset($_GET['sucesso'])): ?>
        <p>Operação realizada com sucesso!</p>
    <?php endif; ?>

    <?php if (isset($_GET['erro'])): ?>
        <p>Não foi possível realizar a operação. Verifique os dados.</p>
    <?php endif; ?>

    <a href="?acao=novo">+ Novo cliente</a>

    <br><br>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CPF/CNPJ</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>

            <?php if (!empty($clientes)): ?>

                <?php foreach ($clientes as $cliente): ?>

                    <tr>
                        <td><?= (int) $cliente['id'] ?></td>

                        <td><?= htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8') ?></td>

                        <td><?= htmlspecialchars($cliente['cpf_cnpj'], ENT_QUOTES, 'UTF-8') ?></td>

                        <td><?= htmlspecialchars($cliente['email'], ENT_QUOTES, 'UTF-8') ?></td>

                        <td><?= htmlspecialchars($cliente['telefone'], ENT_QUOTES, 'UTF-8') ?></td>

                        <td>
                            <a href="?acao=editar&id=<?= (int) $cliente['id'] ?>">
                                Editar
                            </a>

                            <form
                                action="/payflow/app/controllers/ExcluirClienteController.php"
                                method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');"
                            >

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= $csrfToken ?>"
                                >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $cliente['id'] ?>"
                                >

                                <button type="submit">Excluir</button>

                            </form>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="6">Nenhum cliente cadastrado.</td>
                </tr>

            <?php endif; ?>

        </tbody>
    </table>

</body>
</html>
