<?php

require_once 'vendor/autoload.php';

use FocusNFe\NFSeClient;
use FocusNFe\Data\NFSeData;
use FocusNFe\Data\PrestadorData;
use FocusNFe\Data\TomadorData;
use FocusNFe\Data\EnderecoData;
use FocusNFe\Data\ServicoData;
use FocusNFe\Exception\FocusNFeException;

// Configuração do cliente
$sandbox = true;
$token = ($sandbox === true) ? 'OANhdQP6zyY1tdZ9nNwfNB1PyuM0TuZ6':'43peI8WG8C04ptMl0d8OD7lbnc0ljNZU';
$client = new NFSeClient($token, $sandbox); // true para sandbox

try {
    // Criar dados do prestador
    $prestador = new PrestadorData(
        '05386318000176',
        '0177039', // No caso de Fortaleza, fica sem o código verificador
        '2304400' // Código do município (no caso, Fortaleza)
    );

    // Criar dados do endereço
    $endereco = new EnderecoData(
        'AVENIDA JOÃO VALÉRIO',
        '753',
        'NOSSA SENHORA DAS GRAÇAS',
        '3550308',
        'SP',
        '69.053-140',
        ''
    );

    // Criar dados do tomador
    $tomador = new TomadorData(
        '40858894000170',
        'RNI-SM INCORPORADORA IMOBILIARIA 483 LTDA',
        'marcio_ce@hotmail.com',
        $endereco
    );

    // Criar dados do serviço
    $servico = new ServicoData(
        '709', // Item da lista de serviços na tabela do CNAE
        '3811400', // Código CNAE (os sete primeiros dígitos da tabela do CNAE)
        '381140001', // Código de tributação do município (CNAE Completo)
        '11 - COLETA DE CONTÊINER NÃO SEGREGADO - R$ 330,00 = 3.630,00
02 - COLETA DE CONTEINER COM TAMPA - R$ 440,00 = 880,00
REFERENTE JUNHO 2025
OBRA AVENIDA B, 200 OBRA MORADAS DA SERRA - SENADOR CARLOS JEREISSATI - PACATUBA/CE
VENCIMENTO 29/07/2025', //Campo de observação do lançamento
        '2309706',
        true,
        [
            'valor_servicos' => 4510.00,
            'valor_deducoes' => 0.00,
            'valor_pis' => 0.00,
            'valor_cofins' => 0.00,
            'valor_inss' => 0.00,
            'valor_ir' => 0.00,
            'valor_csll' => 0.00,
            'valor_iss' => 0.00,
            'valor_iss_retido' => 0.00,
            'outras_retencoes' => 0.00,
            'base_calculo' => 0.00,
            'aliquota' => 0.00,
            'desconto_incondicionado' => 0.00,
            'desconto_condicionado' => 0.00,
            'percentual_total_tributos' => 0.00,
            'fonte_total_tributos' => 0.00, // IBPT
        ]
    );

    // Criar dados da NFSe
    $nfse = new NFSeData(
        (new DateTime())->format('Y-m-d\TH:i:s'),
        '2', // Natureza da operação (01 - 06, mas Fortaleza incluiu o 7 - Nao incidencia) Acredito que seja para tomadores que não estão no mesmo municipio que o prestador.
        '1', // Regime especial de tributação (Microempresa municipal)
        false, // Optante do Simples Nacional
        false, // Incentivador cultural
        $prestador,
        $tomador,
        $servico
    );

    // Emitir NFSe
    $referencia = time();
    $resultado = $client->nfse()->emitir($referencia, $nfse);

    echo "NFSe emitida com sucesso!<br>";
    echo "Referência: " . $referencia . "<br>";
    echo "Status: " . $resultado['status'] . "<br>";

    if (isset($resultado['numero'])) {
        echo "Número: " . $resultado['numero'] . "<br>";
    }

    // Consultar NFSe
    $consulta = $client->nfse()->consultar($referencia);
    echo "Consulta realizada com sucesso!<br>";

//    // Enviar por email
//    $emails = ['destinatario@exemplo.com.br'];
//    $client->nfse()->enviarPorEmail($referencia, $emails);
//    echo "Email enviado com sucesso!<br>";

} catch (FocusNFeException $e) {
    echo "Erro da API: " . $e->getMessage() . "<br>";
    echo "Código: " . $e->getCode() . "<br>";
    echo "Trace: " . $e->getTraceAsString() . "<br>";
    //print_r($e->getTrace());

    if (!empty($e->getErrors())) {
        echo "Erros detalhados:<br>";
        foreach ($e->getErrors() as $error) {
            echo "- " . $error . "<br>";
        }
    }
} catch (Exception $e) {
    echo "Erro geral: " . $e->getMessage() . "<br>";
}