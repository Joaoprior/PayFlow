<?php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';

exigirLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /payflow/public/");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

$nome = trim($_POST['nome'] ?? '');
$cpf_cnpj = trim($_POST['cpf_cnpj'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');

if ($nome === '' || $cpf_cnpj === '' || $email === '' || $telefone === '') {
    die("Preencha todos os campos.");
}

try {
    if ($id) {
        $sql = "UPDATE clientes
                SET nome = :nome,
                    cpf_cnpj = :cpf_cnpj,
                    email = :email,
                    telefone = :telefone
                WHERE id = :id";

        $consulta = $pdo->prepare($sql);

        $consulta->execute([
            ':nome' => $nome,
            ':cpf_cnpj' => $cpf_cnpj,
            ':email' => $email,
            ':telefone' => $telefone,
            ':id' => $id
        ]);

    } else {
        $sql = "INSERT INTO clientes
                (nome, cpf_cnpj, email, telefone)
                VALUES
                (:nome, :cpf_cnpj, :email, :telefone)";

        $consulta = $pdo->prepare($sql);

        $consulta->execute([
            ':nome' => $nome,
            ':cpf_cnpj' => $cpf_cnpj,
            ':email' => $email,
            ':telefone' => $telefone
        ]);
    }

    header("Location: /payflow/public/");
    exit;

} catch (PDOException $erro) {
    die("Não foi possível salvar os dados do cliente.");
}