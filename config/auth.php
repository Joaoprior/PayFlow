<?php

// Configurações de segurança da sessão
if (session_status() === PHP_SESSION_NONE) {

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_trans_sid', '0');

    $httpsAtivo = (
        isset($_SERVER['HTTPS']) &&
        $_SERVER['HTTPS'] !== 'off'
    );

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $httpsAtivo,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

// Verifica se o usuário está autenticado
function exigirLogin()
{
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: /payflow/public/?acao=login");
        exit;
    }
}