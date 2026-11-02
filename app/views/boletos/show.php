
<?php
$valor = number_format((float) $cobranca['valor'], 2, ',', '.');
$vencimento = date('d/m/Y', strtotime($cobranca['data_vencimento']));
$dataEmissao = date('d/m/Y', strtotime($boleto['criado_em']));
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PayFlow - Consulta de Boleto</title>

    <link rel="stylesheet" href="/payflow/public/css/style.css">
</head>

<body>

    <?php require_once __DIR__ . '/../layout/menu.php'; ?>

    <h1>PayFlow.</h1>

    <h2>Consulta de boleto</h2>

    <p>Confira os dados da sua cobrança.</p>

    <hr>

    <h3>Dados do boleto</h3>

    <p>
        <strong>Nosso número:</strong>
        <?= htmlspecialchars($boleto['nosso_numero']) ?>
    </p>

    <p>
        <strong>Status:</strong>
        <?= htmlspecialchars($boleto['status']) ?>
    </p>

    <p>
        <strong>Data de emissão:</strong>
        <?= $dataEmissao ?>
    </p>

    <hr>

    <h3>Dados do pagador</h3>

    <p>
        <strong>Nome:</strong>
        <?= htmlspecialchars($cobranca['nome_cliente']) ?>
    </p>

    <p>
        <strong>CPF/CNPJ:</strong>
        <?= htmlspecialchars($cobranca['cpf_cnpj'] ?? '') ?>
    </p>

    <p>
        <strong>E-mail:</strong>
        <?= htmlspecialchars($cobranca['email'] ?? '') ?>
    </p>

    <hr>

    <h3>Dados da cobrança</h3>

    <p>
        <strong>Descrição:</strong>
        <?= htmlspecialchars($cobranca['descricao']) ?>
    </p>

    <p>
        <strong>Valor:</strong>
        R$ <?= $valor ?>
    </p>

    <p>
        <strong>Vencimento:</strong>
        <?= $vencimento ?>
    </p>

    <hr>

    <h3>Informações de pagamento</h3>

    <p>
        <strong>Linha digitável:</strong>
        <?= !empty($boleto['linha_digitavel'])
            ? htmlspecialchars($boleto['linha_digitavel'])
            : 'Ainda não disponível' ?>
    </p>

    <p>
        <strong>Código de barras:</strong>
        <?= !empty($boleto['codigo_barras'])
            ? htmlspecialchars($boleto['codigo_barras'])
            : 'Ainda não disponível' ?>
    </p>

    <p>
        <strong>Arquivo PDF:</strong>
        <?= !empty($boleto['arquivo_pdf'])
            ? htmlspecialchars($boleto['arquivo_pdf'])
            : 'Gerado sob demanda' ?>
    </p>

    <hr>

    <p style="color: #d97706;">
        Este documento é demonstrativo. Ainda não possui registro bancário
        nem código válido para pagamento.
    </p>

    <a href="/payflow/app/controllers/BaixarBoletoController.php?id=<?= (int) $boleto['cobranca_id'] ?>"
       target="_blank">
        <button type="button">Baixar boleto em PDF</button>
    </a>

    <br><br>

    <a href="/payflow/public/?acao=cobrancas">
        Voltar para cobranças
    </a>

</body>
</html>
