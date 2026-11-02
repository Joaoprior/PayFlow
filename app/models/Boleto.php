
<?php

class Boleto
{
    private $pdo;

    public function __construct($conexao)
    {
        $this->pdo = $conexao;
    }

    public function cadastrar($dados)
    {
        $sql = "INSERT INTO boletos
                (cobranca_id, nosso_numero, status)
                VALUES
                (:cobranca_id, :nosso_numero, 'Gerado')";

        $consulta = $this->pdo->prepare($sql);

        return $consulta->execute([
            ':cobranca_id' => $dados['cobranca_id'],
            ':nosso_numero' => $dados['nosso_numero']
        ]);
    }

    public function buscarPorCobranca($cobrancaId)
    {
        $sql = "SELECT *
                FROM boletos
                WHERE cobranca_id = :cobranca_id
                ORDER BY id DESC
                LIMIT 1";

        $consulta = $this->pdo->prepare($sql);

        $consulta->execute([
            ':cobranca_id' => $cobrancaId
        ]);

        return $consulta->fetch();
    }

    public function listarPorCobranca($cobrancaId)
    {
        $sql = "SELECT *
                FROM boletos
                WHERE cobranca_id = :cobranca_id
                ORDER BY id DESC";

        $consulta = $this->pdo->prepare($sql);

        $consulta->execute([
            ':cobranca_id' => $cobrancaId
        ]);

        return $consulta->fetchAll();
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT *
                FROM boletos
                WHERE id = :id";

        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([':id' => $id]);

        return $consulta->fetch();
    }
}
