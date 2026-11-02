
<?php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Cliente.php';

exigirLogin();

// Aceita somente requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /payflow/public/");
    exit;
}

// Validação do token CSRF
$token = $_POST['csrf_token'] ?? '';

if (
    !isset($_SESSION['csrf_token']) ||
    !is_string($token) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    http_response_code(403);
    exit("Requisição inválida. Atualize a página e tente novamente.");
}

// Valida o ID
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: /payflow/public/?erro=id");
    exit;
}

try {
    // Verifica se o cliente existe
    $consulta = $pdo->prepare(
        "SELECT id FROM clientes WHERE id = :id"
    );

    $consulta->execute([
        ':id' => $id
    ]);

    $cliente = $consulta->fetch();

    if (!$cliente) {
        header("Location: /payflow/public/?erro=cliente_nao_encontrado");
        exit;
    }

    // Verifica se existem cobranças vinculadas
    $consulta = $pdo->prepare(
        "SELECT COUNT(*) FROM cobrancas WHERE cliente_id = :id"
    );

    $consulta->execute([
        ':id' => $id
    ]);

    $totalCobrancas = (int) $consulta->fetchColumn();

    if ($totalCobrancas > 0) {
        header("Location: /payflow/public/?erro=cliente_com_cobrancas");
        exit;
    }

    // Exclui o cliente
    $clienteModel = new Cliente($pdo);
    $clienteModel->excluir($id);

    header("Location: /payflow/public/?sucesso=cliente_excluido");
    exit;

} catch (PDOException $erro) {
    error_log("Erro ao excluir cliente: " . $erro->getMessage());

    header("Location: /payflow/public/?erro=exclusao");
    exit;
}
