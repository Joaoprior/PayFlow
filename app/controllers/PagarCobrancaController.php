
<?php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Cobranca.php';

exigirLogin();

// Aceita somente requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /payflow/public/?acao=cobrancas");
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

// Valida o ID recebido
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: /payflow/public/?acao=cobrancas&erro=id");
    exit;
}

try {
    $cobrancaModel = new Cobranca($pdo);

    // Verifica se a cobrança existe
    $cobranca = $cobrancaModel->buscarPorId($id);

    if (!$cobranca) {
        header("Location: /payflow/public/?acao=cobrancas&erro=nao_encontrada");
        exit;
    }

    // Impede atualizar uma cobrança já paga
    if ($cobranca['status'] === 'Pago') {
        header("Location: /payflow/public/?acao=cobrancas&aviso=ja_paga");
        exit;
    }

    // Atualiza somente se ainda não estiver paga
    $atualizada = $cobrancaModel->marcarComoPaga($id);

    if (!$atualizada) {
        header("Location: /payflow/public/?acao=cobrancas&aviso=ja_paga");
        exit;
    }

    header("Location: /payflow/public/?acao=cobrancas&sucesso=pagamento");
    exit;

} catch (PDOException $erro) {
    error_log("Erro ao atualizar cobrança: " . $erro->getMessage());

    header("Location: /payflow/public/?acao=cobrancas&erro=pagamento");
    exit;
}
