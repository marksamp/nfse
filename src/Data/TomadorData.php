<?php

declare(strict_types=1);

namespace FocusNFe\Data;

use FocusNFe\Util\Util;

class TomadorData
{
    private ?string $cnpj_cpf = null;
    private string $razao_social;
    private string $email;
    private EnderecoData $endereco;

    public function __construct(
        string $cnpj_cpf,
        string $razao_social,
        string $email,
        EnderecoData $endereco
    ) {
        $this->cnpj_cpf = Util::numbersOfString($cnpj_cpf);
        $this->razao_social = $razao_social;
        $this->email = $email;
        $this->endereco = $endereco;
    }

    public function toArray(): array
    {
        return [
            ((strlen($this->cnpj_cpf) <= 11) ? 'cpf': 'cnpj') => $this->cnpj_cpf,
            'razao_social' => $this->razao_social,
            'email' => $this->email,
            'endereco' => $this->endereco->toArray(),
        ];
    }
}