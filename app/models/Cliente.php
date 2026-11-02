
<?php

class Cliente
{
    private $pdo;

    public function __construct($conexao)
    {
        $this->pdo = $conexao;
    }

    // Listar todos os clientes
    public function listar()
    {
        $sql = "SELECT * FROM clientes ORDER BY nome ASC";

        $consulta = $this->pdo->query($sql);

        return $consulta->fetchAll();
    }

    // Buscar um cliente pelo ID
    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM clientes WHERE id = :id";

        $consulta = $this->pdo->prepare($sql);

        $consulta->execute([
            ':id' => $id
        ]);

        return $consulta->fetch();
    }
    // Excluir cliente
public function excluir($id)
{
    $sql = "DELETE FROM clientes WHERE id = :id";

    $consulta = $this->pdo->prepare($sql);

    return $consulta->execute([
        ':id' => $id
    ]);
}
}
