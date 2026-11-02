
<?php

require_once __DIR__ . '/../../config/auth.php';

// Aceita somente requisições POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /payflow/public/?acao=dashboard");
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

// Limpa os dados armazenados na sessão
$_SESSION = [];

// Remove o cookie da sessão do navegador
if (ini_get('session.use_cookies')) {

    $parametros = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => $parametros['path'],
            'domain' => $parametros['domain'],
            'secure' => $parametros['secure'],
            'httponly' => $parametros['httponly'],
            'samesite' => $parametros['samesite'] ?? 'Lax'
        ]
    );
}

// Destrói a sessão
session_destroy();

// Retorna para a tela de login
header("Location: /payflow/public/?acao=login");
exit;
