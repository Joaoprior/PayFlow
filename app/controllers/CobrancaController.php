
<?php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Cobranca.php';

exigirLogin();

// Permite somente requisições POST
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

// Recebe e trata os dados
$cliente_id = filter_input(INPUT_POST, 'cliente_id', FILTER_VALIDATE_INT);
$descricao = trim($_POST['descricao'] ?? '');
$valorTexto = trim($_POST['valor'] ?? '');
$data_vencimento = trim($_POST['data_vencimento'] ?? '');

// Valida o cliente
if (!$cliente_id || $cliente_id <= 0) {
    header("Location: /payflow/public/?acao=nova_cobranca&erro=cliente");
    exit;
}

// Valida a descrição
if ($descricao === '' || strlen($descricao) > 255) {
    header("Location: /payflow/public/?acao=nova_cobranca&erro=descricao");
    exit;
}

// Valida o valor
if (!preg_match('/^\d+(\.\d{1,2})?$/', $valorTexto)) {
    header("Location: /payflow/public/?acao=nova_cobranca&erro=valor");
    exit;
}

$valor = (float) $valorTexto;

if (!is_finite($valor) || $valor <= 0) {
    header("Location: /payflow/public/?acao=nova_cobranca&erro=valor");
    exit;
}

// Valida a data no formato YYYY-MM-DD
$data = DateTimeImmutable::createFromFormat(
    '!Y-m-d',
    $data_vencimento
);

$errosData = DateTimeImmutable::getLastErrors();

if (
    !$data ||
    ($errosData !== false &&
    ($errosData['warning_count'] > 0 || $errosData['error_count'] > 0)) ||
    $data->format('Y-m-d') !== $data_vencimento
) {
    header("Location: /payflow/public/?acao=nova_cobranca&erro=data");
    exit;
}

try {
    // Verifica se o cliente realmente existe
    $consulta = $pdo->prepare(
        "SELECT id FROM clientes WHERE id = :id"
    );

    $consulta->execute([
        ':id' => $cliente_id
    ]);

    if (!$consulta->fetch()) {
        header("Location: /payflow/public/?acao=nova_cobranca&erro=cliente");
        exit;
    }

    // Cadastra a cobrança
    $cobrancaModel = new Cobranca($pdo);

    $dados = [
        'cliente_id' => $cliente_id,
        'descricao' => $descricao,
        'valor' => $valor,
        'data_vencimento' => $data_vencimento
    ];

    $cobrancaModel->cadastrar($dados);

    header("Location: /payflow/public/?acao=cobrancas&sucesso=1");
    exit;

} catch (PDOException $erro) {
    error_log("Erro ao cadastrar cobrança: " . $erro->getMessage());
    header("Location: /payflow/public/?acao=nova_cobranca&erro=cadastro");
    exit;
}
