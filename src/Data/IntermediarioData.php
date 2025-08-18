<?php

declare(strict_types=1);

namespace FocusNFe\Data;

class IntermediarioData
{
    private string $cnpj_cpf;
    private string $inscricao_municipal;

    public function __construct(
        string $cnpj_cpf,
        string $inscricao_municipal
    ) {
        $this->cnpj_cpf = $cnpj_cpf;
        $this->inscricao_municipal = $inscricao_municipal;
    }

    public function toArray(): array
    {
        return [
            ((strlen($this->cnpj_cpf) <= 11) ? 'cpf': 'cnpj') => $this->cnpj_cpf,
            'inscricao_municipal' => $this->inscricao_municipal
        ];
    }
}