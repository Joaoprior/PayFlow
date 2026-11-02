
<?php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/database.php';

require_once __DIR__ . '/../models/Cobranca.php';
require_once __DIR__ . '/../models/Boleto.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

exigirLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("ID do boleto inválido.");
}

$cobrancaModel = new Cobranca($pdo);
$boletoModel = new Boleto($pdo);

$cobranca = $cobrancaModel->buscarPorId($id);
$boleto = $boletoModel->buscarPorCobranca($id);

if (!$cobranca || !$boleto) {
    die("Boleto ou cobrança não encontrados.");
}

$valor = number_format((float) $cobranca['valor'], 2, ',', '.');

$vencimento = date(
    'd/m/Y',
    strtotime($cobranca['data_vencimento'])
);

$dataEmissao = date(
    'd/m/Y',
    strtotime($boleto['criado_em'])
);

$nomeCliente = htmlspecialchars(
    $cobranca['nome_cliente'],
    ENT_QUOTES,
    'UTF-8'
);

$descricao = htmlspecialchars(
    $cobranca['descricao'],
    ENT_QUOTES,
    'UTF-8'
);

$nossoNumero = htmlspecialchars(
    $boleto['nosso_numero'],
    ENT_QUOTES,
    'UTF-8'
);

$cpfCnpj = htmlspecialchars(
    $cobranca['cpf_cnpj'] ?? 'Não informado',
    ENT_QUOTES,
    'UTF-8'
);

$email = htmlspecialchars(
    $cobranca['email'] ?? 'Não informado',
    ENT_QUOTES,
    'UTF-8'
);

$options = new Options();
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);

$html = "
<!DOCTYPE html>
<html lang='pt-BR'>

<head>
    <meta charset='UTF-8'>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Helvetica, sans-serif;
            color: #1e293b;
            font-size: 12px;
            margin: 0;
        }

        .container {
            width: 100%;
        }

        .header {
            background-color: #111827;
            color: #ffffff;
            padding: 25px;
        }

        .logo {
            font-size: 27px;
            font-weight: bold;
            margin: 0;
        }

        .subtitle {
            color: #cbd5e1;
            margin-top: 6px;
        }

        .badge {
            background-color: #dbeafe;
            color: #1d4ed8;
            padding: 7px 10px;
            display: inline-block;
            margin-top: 15px;
            font-size: 10px;
            font-weight: bold;
        }

        .content {
            padding: 25px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
            margin-top: 22px;
            margin-bottom: 12px;
        }

        .info-box {
            border: 1px solid #e2e8f0;
            padding: 13px;
            margin-bottom: 10px;
        }

        .label {
            color: #64748b;
            font-size: 10px;
            margin-bottom: 5px;
        }

        .value {
            color: #0f172a;
            font-size: 12px;
            font-weight: bold;
        }

        .highlight {
            background-color: #f1f5f9;
            padding: 18px;
            margin-top: 15px;
        }

        .amount {
            font-size: 25px;
            font-weight: bold;
            color: #111827;
            margin-top: 7px;
        }

        .row {
            width: 100%;
        }

        .column {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .barcode {
            border: 1px dashed #94a3b8;
            background-color: #f8fafc;
            text-align: center;
            padding: 22px;
            margin-top: 15px;
            color: #64748b;
            font-weight: bold;
        }

        .notice {
            background-color: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            padding: 14px;
            margin-top: 22px;
            line-height: 1.5;
        }

        .footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            margin-top: 25px;
            color: #64748b;
            font-size: 10px;
            text-align: center;
        }

    </style>
</head>

<body>

<div class='container'>

    <div class='header'>
        <p class='logo'>PayFlow.</p>

        <p class='subtitle'>
            Gestão inteligente de cobranças
        </p>

        <span class='badge'>
            DOCUMENTO DEMONSTRATIVO
        </span>
    </div>

    <div class='content'>

        <h2>Documento de cobrança</h2>

        <p>
            Confira abaixo os dados referentes à cobrança cadastrada
            no sistema PayFlow.
        </p>

        <div class='section-title'>
            IDENTIFICAÇÃO DO DOCUMENTO
        </div>

        <div class='row'>

            <div class='column'>
                <div class='info-box'>
                    <div class='label'>Nosso número</div>
                    <div class='value'>{$nossoNumero}</div>
                </div>
            </div>

            <div class='column'>
                <div class='info-box'>
                    <div class='label'>Data de emissão</div>
                    <div class='value'>{$dataEmissao}</div>
                </div>
            </div>

        </div>

        <div class='section-title'>
            DADOS DO PAGADOR
        </div>

        <div class='info-box'>
            <div class='label'>Nome completo</div>
            <div class='value'>{$nomeCliente}</div>
        </div>

        <div class='row'>

            <div class='column'>
                <div class='info-box'>
                    <div class='label'>CPF / CNPJ</div>
                    <div class='value'>{$cpfCnpj}</div>
                </div>
            </div>

            <div class='column'>
                <div class='info-box'>
                    <div class='label'>E-mail</div>
                    <div class='value'>{$email}</div>
                </div>
            </div>

        </div>

        <div class='section-title'>
            DETALHES DA COBRANÇA
        </div>

        <div class='info-box'>
            <div class='label'>Descrição</div>
            <div class='value'>{$descricao}</div>
        </div>

        <div class='highlight'>

            <div class='row'>

                <div class='column'>
                    <div class='label'>VALOR DA COBRANÇA</div>
                    <div class='amount'>R$ {$valor}</div>
                </div>

                <div class='column'>
                    <div class='label'>DATA DE VENCIMENTO</div>
                    <div class='amount'>{$vencimento}</div>
                </div>

            </div>

        </div>

        <div class='section-title'>
            INFORMAÇÕES DE PAGAMENTO
        </div>

        <div class='barcode'>
            CÓDIGO DE BARRAS NÃO DISPONÍVEL<br><br>
            DOCUMENTO SEM REGISTRO BANCÁRIO
        </div>

        <div class='notice'>
            <strong>ATENÇÃO: DOCUMENTO DEMONSTRATIVO</strong><br>

            Este documento foi gerado pelo sistema PayFlow para
            demonstração de funcionalidades. Ele não possui registro
            bancário, linha digitável ou código de barras válido.
            Portanto, não pode ser utilizado para realizar pagamentos.
        </div>

        <div class='footer'>
            PayFlow - Sistema de gerenciamento de cobranças<br>
            Documento gerado eletronicamente em {$dataEmissao}
        </div>

    </div>

</div>

</body>
</html>
";

$dompdf->loadHtml($html, 'UTF-8');

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream(
    "payflow-cobranca-{$id}.pdf",
    [
        "Attachment" => true
    ]
);

exit;
