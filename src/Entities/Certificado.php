<?php

namespace NFSe\Entities;

class Certificado
{
    private string $arquivo;
    private string $senha;
    private string $conteudo;

    public function __construct(string $arquivo, string $senha)
    {
        $this->arquivo = $arquivo;
        $this->senha = $senha;

        if (!file_exists($arquivo)) {
            throw new \InvalidArgumentException("Arquivo de certificado não encontrado: {$arquivo}");
        }

        $this->conteudo = file_get_contents($arquivo);

        if (!$this->validarCertificado()) {
            throw new \InvalidArgumentException("Certificado inválido ou senha incorreta");
        }
    }

    public function getArquivo(): string
    {
        return $this->arquivo;
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function getConteudo(): string
    {
        return $this->conteudo;
    }

    public function getCertificadoPem(): string
    {
        $cert = [];
        if (!openssl_pkcs12_read($this->conteudo, $cert, $this->senha)) {
            throw new \RuntimeException("Erro ao ler certificado PKCS12");
        }

        return $cert['cert'];
    }

    public function getChavePrivadaPem(): string
    {
        $cert = [];
        if (!openssl_pkcs12_read($this->conteudo, $cert, $this->senha)) {
            throw new \RuntimeException("Erro ao ler certificado PKCS12");
        }

        return $cert['pkey'];
    }

    public function getChainCertificados(): array
    {
        $cert = [];
        if (!openssl_pkcs12_read($this->conteudo, $cert, $this->senha)) {
            throw new \RuntimeException("Erro ao ler certificado PKCS12");
        }

        return $cert['extracerts'] ?? [];
    }

    private function validarCertificado(): bool
    {
        $cert = [];
        return openssl_pkcs12_read($this->conteudo, $cert, $this->senha);
    }

    public function isExpirado(): bool
    {
        $certificado = openssl_x509_parse($this->getCertificadoPem());
        return time() > $certificado['validTo_time_t'];
    }

    public function getDataVencimento(): \DateTime
    {
        $certificado = openssl_x509_parse($this->getCertificadoPem());
        return new \DateTime('@' . $certificado['validTo_time_t']);
    }

    public function getCnpj(): string
    {
        $certificado = openssl_x509_parse($this->getCertificadoPem());
        $subject = $certificado['subject'];

        // Extrair CNPJ do subject
        if (isset($subject['CN'])) {
            preg_match('/(\d{2}\.?\d{3}\.?\d{3}\/?\d{4}-?\d{2})/', $subject['CN'], $matches);
            if (isset($matches[1])) {
                return preg_replace('/\D/', '', $matches[1]);
            }
        }

        return '';
    }
}