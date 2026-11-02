
<?php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

// Aceita somente requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /payflow/public/?acao=login");
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
$email = strtolower(trim($_POST['email'] ?? ''));
$senha = $_POST['senha'] ?? '';

// Valida os campos
if (
    $email === '' ||
    $senha === '' ||
    strlen($email) > 254
) {
    header("Location: /payflow/public/?acao=login&erro=1");
    exit;
}

// Valida o formato do e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: /payflow/public/?acao=login&erro=1");
    exit;
}

try {
    $usuarioModel = new Usuario($pdo);

    // Busca o usuário pelo e-mail
    $usuario = $usuarioModel->buscarPorEmail($email);

    // Verifica usuário e senha
    if (
        !$usuario ||
        !password_verify($senha, $usuario['senha'])
    ) {
        header("Location: /payflow/public/?acao=login&erro=1");
        exit;
    }

    // Renova o identificador da sessão
    session_regenerate_id(true);

    // Armazena os dados do usuário na sessão
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];

    // Redireciona para o dashboard
    header("Location: /payflow/public/?acao=dashboard");
    exit;

} catch (PDOException $erro) {
    error_log("Erro durante o login: " . $erro->getMessage());

    header("Location: /payflow/public/?acao=login&erro=1");
    exit;
}
