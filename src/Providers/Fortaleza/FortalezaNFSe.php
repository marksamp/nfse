<?php

namespace NFSe\Providers\Fortaleza;

use NFSe\Contracts\NFSeInterface;
use NFSe\Entities\NotaFiscal;
use NFSe\Entities\Certificado;
use NFSe\Services\XmlSigner;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FortalezaNFSe implements NFSeInterface
{
    private const URL_PRODUCAO = 'https://iss.fortaleza.ce.gov.br/grpfor/iss/ws/nfse.wsdl';
    private const URL_HOMOLOGACAO = 'https://isshml.fortaleza.ce.gov.br/grpfor/iss/ws/nfse.wsdl';

    private Client $client;
    private ?Certificado $certificado = null;
    private bool $homologacao = true;
    private XmlBuilder $xmlBuilder;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false,
        ]);

        $this->xmlBuilder = new XmlBuilder();
    }

    public function setCertificado(Certificado $certificado): void
    {
        $this->certificado = $certificado;
    }

    public function setHomologacao(bool $homologacao): void
    {
        $this->homologacao = $homologacao;
    }

    public function emitir(NotaFiscal $notaFiscal): array
    {
        $xml = $this->xmlBuilder->buildRps($notaFiscal);

        if ($this->certificado) {
            $signer = new XmlSigner($this->certificado);
            $xml = $signer->assinarXml($xml, 'Rps');
        }

        $soapEnvelope = $this->buildSoapEnvelope($xml, 'GerarNfse');

        return $this->enviarRequisicao($soapEnvelope, 'GerarNfse');
    }

    public function cancelar(string $numeroNota, string $motivo): array
    {
        $xml = $this->xmlBuilder->buildCancelamento($numeroNota, $motivo);

        if ($this->certificado) {
            $signer = new XmlSigner($this->certificado);
            $xml = $signer->assinarXml($xml, 'Pedido');
        }

        $soapEnvelope = $this->buildSoapEnvelope($xml, 'CancelarNfse');

        return $this->enviarRequisicao($soapEnvelope, 'CancelarNfse');
    }

    public function consultar(string $numeroNota): array
    {
        $xml = $this->xmlBuilder->buildConsultaNota($numeroNota);
        $soapEnvelope = $this->buildSoapEnvelope($xml, 'ConsultarNfse');

        return $this->enviarRequisicao($soapEnvelope, 'ConsultarNfse');
    }

    public function consultarLoteRps(string $numeroLote): array
    {
        $xml = $this->xmlBuilder->buildConsultaLoteRps($numeroLote);
        $soapEnvelope = $this->buildSoapEnvelope($xml, 'ConsultarLoteRps');

        return $this->enviarRequisicao($soapEnvelope, 'ConsultarLoteRps');
    }

    private function buildSoapEnvelope(string $xml, string $metodo): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' .
            '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" ' .
            'xmlns:nfse="http://nfse.fortaleza.ce.gov.br/">' .
            '<soap:Header/>' .
            '<soap:Body>' .
            "<nfse:{$metodo}>" .
            '<nfse:xml>' . htmlspecialchars($xml) . '</nfse:xml>' .
            "</nfse:{$metodo}>" .
            '</soap:Body>' .
            '</soap:Envelope>';
    }

    private function enviarRequisicao(string $soapEnvelope, string $action): array
    {
        $url = $this->homologacao ? self::URL_HOMOLOGACAO : self::URL_PRODUCAO;

        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction' => $action,
                ],
                'body' => $soapEnvelope,
            ]);

            $body = $response->getBody()->getContents();

            return $this->parseResponse($body);

        } catch (RequestException $e) {
            throw new \RuntimeException(
                'Erro na requisição: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    private function parseResponse(string $response): array
    {
        $dom = new \DOMDocument();
        $dom->loadXML($response);

        // Remover namespaces para facilitar parsing
        $xpath = new \DOMXPath($dom);

        // Verificar se há erros
        $erros = $xpath->query('//ListaMensagemRetorno/MensagemRetorno');
        if ($erros->length > 0) {
            $mensagensErro = [];
            foreach ($erros as $erro) {
                $mensagensErro[] = [
                    'codigo' => $xpath->evaluate('string(Codigo)', $erro),
                    'mensagem' => $xpath->evaluate('string(Mensagem)', $erro),
                    'correcao' => $xpath->evaluate('string(Correcao)', $erro),
                ];
            }

            return [
                'sucesso' => false,
                'erros' => $mensagensErro,
            ];
        }

        // Extrair dados da resposta
        $numeroNfse = $xpath->evaluate('string(//CompNfse/Nfse/InfNfse/Numero)');
        $codigoVerificacao = $xpath->evaluate('string(//CompNfse/Nfse/InfNfse/CodigoVerificacao)');
        $dataEmissao = $xpath->evaluate('string(//CompNfse/Nfse/InfNfse/DataEmissao)');

        return [
            'sucesso' => true,
            'numero_nfse' => $numeroNfse,
            'codigo_verificacao' => $codigoVerificacao,
            'data_emissao' => $dataEmissao,
            'xml_resposta' => $response,
        ];
    }
}