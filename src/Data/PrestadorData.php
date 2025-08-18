<?php

declare(strict_types=1);

namespace FocusNFe\Data;

class PrestadorData
{
    private string $cnpj;
    private string $inscricao_municipal;
    private string $codigo_municipio;

    public function __construct(
        string $cnpj,
        string $inscricao_municipal,
        string $codigo_municipio
    ) {
        $this->cnpj = $cnpj;
        $this->inscricao_municipal = $inscricao_municipal;
        $this->codigo_municipio = $codigo_municipio;
    }

    public function toArray(): array
    {
        return [
            'cnpj' => $this->cnpj,
            'inscricao_municipal' => $this->inscricao_municipal,
            'codigo_municipio' => $this->codigo_municipio,
        ];
    }
}