
<?php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

// Aceita somente requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /payflow/public/?acao=cadastro");
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

// Recebe os dados
$nome = trim($_POST['nome'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$senha = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

// Valida campos obrigatórios
if (
    $nome === '' ||
    $email === '' ||
    $senha === '' ||
    $confirmar_senha === ''
) {
    header("Location: /payflow/public/?acao=cadastro&erro=campos");
    exit;
}

// Valida o tamanho do nome
if (strlen($nome) > 150) {
    header("Location: /payflow/public/?acao=cadastro&erro=nome");
    exit;
}

// Valida o e-mail
if (
    strlen($email) > 254 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    header("Location: /payflow/public/?acao=cadastro&erro=email");
    exit;
}

// Valida o tamanho da senha
if (strlen($senha) < 8 || strlen($senha) > 72) {
    header("Location: /payflow/public/?acao=cadastro&erro=senha");
    exit;
}

// Confirma se as senhas são iguais
if ($senha !== $confirmar_senha) {
    header("Location: /payflow/public/?acao=cadastro&erro=confirmacao");
    exit;
}

// Gera o hash da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

if ($senha_hash === false) {
    header("Location: /payflow/public/?acao=cadastro&erro=cadastro");
    exit;
}

try {
    $usuarioModel = new Usuario($pdo);

    $usuarioModel->cadastrar([
        'nome' => $nome,
        'email' => $email,
        'senha' => $senha_hash
    ]);

    header("Location: /payflow/public/?acao=cadastro&sucesso=1");
    exit;

} catch (PDOException $erro) {
    error_log("Erro ao cadastrar usuário: " . $erro->getMessage());

    // Verifica se o e-mail já está cadastrado
    if ($erro->getCode() === '23000') {
        header("Location: /payflow/public/?acao=cadastro&erro=duplicado");
        exit;
    }

    header("Location: /payflow/public/?acao=cadastro&erro=cadastro");
    exit;
}
