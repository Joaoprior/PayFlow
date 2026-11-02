
<?php

class Cobranca
{
    private $pdo;

    public function __construct($conexao)
    {
        $this->pdo = $conexao;
    }

    public function listar()
    {
        $sql = "SELECT
                    cobrancas.*,
                    clientes.nome AS nome_cliente,
                    CASE
                        WHEN cobrancas.status = 'Pago' THEN 'Pago'
                        WHEN cobrancas.data_vencimento < CURDATE() THEN 'Vencido'
                        ELSE 'Pendente'
                    END AS status_atual
                FROM cobrancas
                INNER JOIN clientes
                    ON cobrancas.cliente_id = clientes.id
                ORDER BY cobrancas.data_vencimento ASC";

        $consulta = $this->pdo->query($sql);
        return $consulta->fetchAll();
    }

    public function buscarPorId($id)
    {
        $sql = "SELECT
                    cobrancas.*,
                    clientes.nome AS nome_cliente,
                    clientes.cpf_cnpj,
                    clientes.email
                FROM cobrancas
                INNER JOIN clientes
                    ON cobrancas.cliente_id = clientes.id
                WHERE cobrancas.id = :id";

        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([':id' => $id]);

        return $consulta->fetch();
    }

    public function cadastrar($dados)
    {
        $sql = "INSERT INTO cobrancas
                (cliente_id, descricao, valor, data_vencimento, status)
                VALUES
                (:cliente_id, :descricao, :valor, :data_vencimento, 'Pendente')";

        $consulta = $this->pdo->prepare($sql);

        return $consulta->execute([
            ':cliente_id' => $dados['cliente_id'],
            ':descricao' => $dados['descricao'],
            ':valor' => $dados['valor'],
            ':data_vencimento' => $dados['data_vencimento']
        ]);
    }

    public function marcarComoPaga($id)
    {
        $sql = "UPDATE cobrancas
                SET status = 'Pago'
                WHERE id = :id
                AND status != 'Pago'";

        $consulta = $this->pdo->prepare($sql);
        $consulta->execute([':id' => $id]);

        // Retorna true somente quando uma cobrança foi atualizada
        return $consulta->rowCount() > 0;
    }

    public function resumoFinanceiro()
    {
        $sql = "SELECT
                    COALESCE(SUM(
                        CASE WHEN status = 'Pago'
                        THEN valor ELSE 0 END
                    ), 0) AS total_recebido,

                    COALESCE(SUM(
                        CASE WHEN status != 'Pago'
                        AND data_vencimento >= CURDATE()
                        THEN valor ELSE 0 END
                    ), 0) AS total_pendente,

                    COALESCE(SUM(
                        CASE WHEN status != 'Pago'
                        AND data_vencimento < CURDATE()
                        THEN valor ELSE 0 END
                    ), 0) AS total_vencido

                FROM cobrancas";

        $consulta = $this->pdo->query($sql);
        return $consulta->fetch();
    }
}
