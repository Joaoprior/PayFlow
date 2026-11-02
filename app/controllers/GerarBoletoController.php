
<?php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';

require_once __DIR__ . '/../models/Cobranca.php';
require_once __DIR__ . '/../models/Boleto.php';

exigirLogin();

// Aceita somente requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /payflow/public/?acao=cobrancas");
    exit;
}

// Valida o token CSRF
$token = $_POST['csrf_token'] ?? '';

if (
    !isset($_SESSION['csrf_token']) ||
    !is_string($token) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    http_response_code(403);
    exit("Requisição inválida. Atualize a página e tente novamente.");
}

// Valida o ID da cobrança
$cobrancaId = filter_input(INPUT_POST, 'cobranca_id', FILTER_VALIDATE_INT);

if (!$cobrancaId || $cobrancaId <= 0) {
    header("Location: /payflow/public/?acao=cobrancas&erro=id");
    exit;
}

$cobrancaModel = new Cobranca($pdo);
$boletoModel = new Boleto($pdo);

try {
    // Inicia uma transação
    $pdo->beginTransaction();

    // Bloqueia a cobrança durante a geração do boleto
    $consulta = $pdo->prepare(
        "SELECT id, status
         FROM cobrancas
         WHERE id = :id
         FOR UPDATE"
    );

    $consulta->execute([':id' => $cobrancaId]);
    $cobranca = $consulta->fetch();

    if (!$cobranca) {
        $pdo->rollBack();

        header("Location: /payflow/public/?acao=cobrancas&erro=nao_encontrada");
        exit;
    }

    if ($cobranca['status'] === 'Pago') {
        $pdo->rollBack();

        header("Location: /payflow/public/?acao=cobrancas&erro=paga");
        exit;
    }

    // Verifica se já existe boleto para essa cobrança
    $boletoExistente = $boletoModel->buscarPorCobranca($cobrancaId);

    if ($boletoExistente) {
        $pdo->rollBack();

        header("Location: /payflow/public/?acao=cobrancas&boleto=existente");
        exit;
    }

    // Gera um identificador demonstrativo
    $nossoNumero = 'PF' . date('Ymd') . random_int(100000, 999999);

    $boletoModel->cadastrar([
        'cobranca_id' => $cobrancaId,
        'nosso_numero' => $nossoNumero
    ]);

    // Confirma a operação
    $pdo->commit();

    header("Location: /payflow/public/?acao=cobrancas&boleto=gerado");
    exit;

} catch (PDOException $erro) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Código 23000: violação de restrição de integridade,
    // incluindo a restrição UNIQUE de cobrança.
    if ($erro->getCode() === '23000') {
        header("Location: /payflow/public/?acao=cobrancas&boleto=existente");
        exit;
    }

    error_log("Erro ao gerar boleto: " . $erro->getMessage());

    header("Location: /payflow/public/?acao=cobrancas&erro=geracao");
    exit;

} catch (Throwable $erro) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log("Erro inesperado ao gerar boleto: " . $erro->getMessage());

    header("Location: /payflow/public/?acao=cobrancas&erro=geracao");
    exit;
}
