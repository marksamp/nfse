<?php

namespace NFSe\Entities;

class NotaFiscal
{
    private string $numeroRps;
    private string $serieRps;
    private int $tipoRps;
    private \DateTime $dataEmissao;
    private int $naturezaOperacao;
    private int $regimeEspecialTributacao;
    private bool $optanteSimplesNacional;
    private bool $incentivadorCultural;
    private int $status;

    // Prestador
    private string $prestadorCnpj;
    private string $prestadorInscricaoMunicipal;

    // Tomador
    private string $tomadorCnpj;
    private string $tomadorRazaoSocial;
    private string $tomadorEmail;
    private string $tomadorEndereco;
    private string $tomadorNumero;
    private string $tomadorComplemento;
    private string $tomadorBairro;
    private string $tomadorCodigoMunicipio;
    private string $tomadorUf;
    private string $tomadorCep;
    private string $tomadorTelefone;

    // Serviço
    private array $servicos = [];
    private float $valorServicos;
    private float $valorDeducoes;
    private float $valorPis;
    private float $valorCofins;
    private float $valorInss;
    private float $valorIr;
    private float $valorCsll;
    private float $issRetido;
    private float $valorIss;
    private float $aliquota;
    private float $descontoIncondicionado;
    private float $descontoCondicionado;
    private string $itemListaServico;
    private string $codigoCnae;
    private string $codigoTributacaoMunicipio;
    private string $discriminacao;
    private string $codigoMunicipio;

    public function __construct(
        string $numeroRps,
        string $serieRps,
        int $tipoRps,
        \DateTime $dataEmissao
    ) {
        $this->numeroRps = $numeroRps;
        $this->serieRps = $serieRps;
        $this->tipoRps = $tipoRps;
        $this->dataEmissao = $dataEmissao;
    }

    // Getters
    public function getNumeroRps(): string { return $this->numeroRps; }
    public function getSerieRps(): string { return $this->serieRps; }
    public function getTipoRps(): int { return $this->tipoRps; }
    public function getDataEmissao(): \DateTime { return $this->dataEmissao; }
    public function getNaturezaOperacao(): int { return $this->naturezaOperacao ?? 1; }
    public function getRegimeEspecialTributacao(): int { return $this->regimeEspecialTributacao ?? 0; }
    public function isOptanteSimplesNacional(): bool { return $this->optanteSimplesNacional ?? false; }
    public function isIncentivadorCultural(): bool { return $this->incentivadorCultural ?? false; }
    public function getStatus(): int { return $this->status ?? 1; }

    // Prestador
    public function getPrestadorCnpj(): string { return $this->prestadorCnpj; }
    public function getPrestadorInscricaoMunicipal(): string { return $this->prestadorInscricaoMunicipal; }

    // Tomador
    public function getTomadorCnpj(): string { return $this->tomadorCnpj; }
    public function getTomadorRazaoSocial(): string { return $this->tomadorRazaoSocial; }
    public function getTomadorEmail(): string { return $this->tomadorEmail; }
    public function getTomadorEndereco(): string { return $this->tomadorEndereco; }
    public function getTomadorNumero(): string { return $this->tomadorNumero; }
    public function getTomadorComplemento(): string { return $this->tomadorComplemento ?? ''; }
    public function getTomadorBairro(): string { return $this->tomadorBairro; }
    public function getTomadorCodigoMunicipio(): string { return $this->tomadorCodigoMunicipio; }
    public function getTomadorUf(): string { return $this->tomadorUf; }
    public function getTomadorCep(): string { return $this->tomadorCep; }
    public function getTomadorTelefone(): string { return $this->tomadorTelefone ?? ''; }

    // Serviço
    public function getValorServicos(): float { return $this->valorServicos; }
    public function getValorDeducoes(): float { return $this->valorDeducoes ?? 0; }
    public function getValorPis(): float { return $this->valorPis ?? 0; }
    public function getValorCofins(): float { return $this->valorCofins ?? 0; }
    public function getValorInss(): float { return $this->valorInss ?? 0; }
    public function getValorIr(): float { return $this->valorIr ?? 0; }
    public function getValorCsll(): float { return $this->valorCsll ?? 0; }
    public function getIssRetido(): float { return $this->issRetido ?? 0; }
    public function getValorIss(): float { return $this->valorIss ?? 0; }
    public function getAliquota(): float { return $this->aliquota; }
    public function getDescontoIncondicionado(): float { return $this->descontoIncondicionado ?? 0; }
    public function getDescontoCondicionado(): float { return $this->descontoCondicionado ?? 0; }
    public function getItemListaServico(): string { return $this->itemListaServico; }
    public function getCodigoCnae(): string { return $this->codigoCnae ?? ''; }
    public function getCodigoTributacaoMunicipio(): string { return $this->codigoTributacaoMunicipio ?? ''; }
    public function getDiscriminacao(): string { return $this->discriminacao; }
    public function getCodigoMunicipio(): string { return $this->codigoMunicipio; }

    // Setters
    public function setNaturezaOperacao(int $naturezaOperacao): self { $this->naturezaOperacao = $naturezaOperacao; return $this; }
    public function setRegimeEspecialTributacao(int $regimeEspecialTributacao): self { $this->regimeEspecialTributacao = $regimeEspecialTributacao; return $this; }
    public function setOptanteSimplesNacional(bool $optanteSimplesNacional): self { $this->optanteSimplesNacional = $optanteSimplesNacional; return $this; }
    public function setIncentivadorCultural(bool $incentivadorCultural): self { $this->incentivadorCultural = $incentivadorCultural; return $this; }
    public function setStatus(int $status): self { $this->status = $status; return $this; }

    // Prestador
    public function setPrestador(string $cnpj, string $inscricaoMunicipal): self {
        $this->prestadorCnpj = $cnpj;
        $this->prestadorInscricaoMunicipal = $inscricaoMunicipal;
        return $this;
    }

    // Tomador
    public function setTomador(
        string $cnpj,
        string $razaoSocial,
        string $email,
        string $endereco,
        string $numero,
        string $bairro,
        string $codigoMunicipio,
        string $uf,
        string $cep,
        string $complemento = '',
        string $telefone = ''
    ): self {
        $this->tomadorCnpj = $cnpj;
        $this->tomadorRazaoSocial = $razaoSocial;
        $this->tomadorEmail = $email;
        $this->tomadorEndereco = $endereco;
        $this->tomadorNumero = $numero;
        $this->tomadorComplemento = $complemento;
        $this->tomadorBairro = $bairro;
        $this->tomadorCodigoMunicipio = $codigoMunicipio;
        $this->tomadorUf = $uf;
        $this->tomadorCep = $cep;
        $this->tomadorTelefone = $telefone;
        return $this;
    }

    // Serviço
    public function setServico(
        float $valorServicos,
        string $itemListaServico,
        string $discriminacao,
        string $codigoMunicipio,
        float $aliquota,
        float $valorDeducoes = 0,
        float $valorPis = 0,
        float $valorCofins = 0,
        float $valorInss = 0,
        float $valorIr = 0,
        float $valorCsll = 0,
        float $issRetido = 0,
        float $valorIss = 0,
        float $descontoIncondicionado = 0,
        float $descontoCondicionado = 0,
        string $codigoCnae = '',
        string $codigoTributacaoMunicipio = ''
    ): self {
        $this->valorServicos = $valorServicos;
        $this->itemListaServico = $itemListaServico;
        $this->discriminacao = $discriminacao;
        $this->codigoMunicipio = $codigoMunicipio;
        $this->aliquota = $aliquota;
        $this->valorDeducoes = $valorDeducoes;
        $this->valorPis = $valorPis;
        $this->valorCofins = $valorCofins;
        $this->valorInss = $valorInss;
        $this->valorIr = $valorIr;
        $this->valorCsll = $valorCsll;
        $this->issRetido = $issRetido;
        $this->valorIss = $valorIss;
        $this->descontoIncondicionado = $descontoIncondicionado;
        $this->descontoCondicionado = $descontoCondicionado;
        $this->codigoCnae = $codigoCnae;
        $this->codigoTributacaoMunicipio = $codigoTributacaoMunicipio;
        return $this;
    }
}