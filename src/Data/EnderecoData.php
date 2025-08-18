<?php

declare(strict_types=1);

namespace FocusNFe\Data;
use FocusNFe\Util\Util;
class EnderecoData
{
    private string $logradouro;
    private string $numero;
    private string $bairro;
    private string $codigo_municipio;
    private string $uf;
    private string $cep;
    private ?string $complemento = null;

    public function __construct(
        string $logradouro,
        string $numero,
        string $bairro,
        string $codigo_municipio,
        string $uf,
        string $cep,
        string $complemento,
    ) {
        $this->logradouro = $logradouro;
        $this->numero = $numero;
        $this->bairro = $bairro;
        $this->codigo_municipio = $codigo_municipio;
        $this->uf = $uf;
        $this->cep = $cep;
        $this->complemento = $complemento;
    }

    public function toArray(): array
    {
        $data = [
            'logradouro' => $this->logradouro,
            'numero' => $this->numero,
            'bairro' => $this->bairro,
            'codigo_municipio' => $this->codigo_municipio,
            'uf' => $this->uf,
            'cep' => Util::numbersOfString($this->cep),
        ];

        if (!empty($this->complemento)) {
            $data['complemento'] = $this->complemento;
        }

        return $data;
    }
}