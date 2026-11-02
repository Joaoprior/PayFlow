<?php

class Usuario
{
    private $pdo;

    public function __construct($conexao)
    {
        $this->pdo = $conexao;
    }

    public function cadastrar($dados)
    {
        $sql = "INSERT INTO usuarios (nome, email, senha)
                VALUES (:nome, :email, :senha)";

        $consulta = $this->pdo->prepare($sql);

        return $consulta->execute([
            ':nome' => $dados['nome'],
            ':email' => $dados['email'],
            ':senha' => $dados['senha']
        ]);
    }

    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email";

        $consulta = $this->pdo->prepare($sql);

        $consulta->execute([
            ':email' => $email
        ]);

        return $consulta->fetch();
    }
}