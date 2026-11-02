
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

require_once __DIR__ . '/../app/models/Cliente.php';
require_once __DIR__ . '/../app/models/Cobranca.php';
require_once __DIR__ . '/../app/models/Boleto.php';

$acao = $_GET['acao'] ?? '';

if ($acao === 'cadastro') {
    require_once __DIR__ . '/../app/views/auth/cadastro.php';
    exit;
}

if ($acao === 'login') {
    if (isset($_SESSION['usuario_id'])) {
        header("Location: /payflow/public/?acao=dashboard");
        exit;
    }

    require_once __DIR__ . '/../app/views/auth/login.php';
    exit;
}

exigirLogin();

$clienteModel = new Cliente($pdo);
$cobrancaModel = new Cobranca($pdo);
$boletoModel = new Boleto($pdo);

if ($acao === 'dashboard') {
    $resumo = $cobrancaModel->resumoFinanceiro();
    require_once __DIR__ . '/../app/views/dashboard/index.php';

} elseif ($acao === 'novo') {
    require_once __DIR__ . '/../app/views/clientes/create.php';

} elseif ($acao === 'editar') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        die("ID de cliente inválido.");
    }

    $cliente = $clienteModel->buscarPorId($id);

    if (!$cliente) {
        die("Cliente não encontrado.");
    }

    require_once __DIR__ . '/../app/views/clientes/edit.php';

} elseif ($acao === 'nova_cobranca') {
    $clientes = $clienteModel->listar();

    if (empty($clientes)) {
        die("Cadastre pelo menos um cliente antes de criar uma cobrança.");
    }

    require_once __DIR__ . '/../app/views/cobrancas/create.php';

} elseif ($acao === 'cobrancas') {
    $cobrancas = $cobrancaModel->listar();

    foreach ($cobrancas as &$cobranca) {
        $cobranca['boleto'] = $boletoModel->buscarPorCobranca($cobranca['id']);
    }

    unset($cobranca);

    require_once __DIR__ . '/../app/views/cobrancas/index.php';

} elseif ($acao === 'ver_boleto') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        die("ID da cobrança inválido.");
    }

    $boleto = $boletoModel->buscarPorCobranca($id);

    if (!$boleto) {
        die("Esta cobrança ainda não possui boleto cadastrado.");
    }

    $cobranca = $cobrancaModel->buscarPorId($id);

    if (!$cobranca) {
        die("Cobrança não encontrada.");
    }

    require_once __DIR__ . '/../app/views/boletos/show.php';

} else {
    $clientes = $clienteModel->listar();
    require_once __DIR__ . '/../app/views/clientes/index.php';
}
