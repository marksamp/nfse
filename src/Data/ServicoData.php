<?php

declare(strict_types=1);

namespace FocusNFe\Data;

class ServicoData
{
    private string $item_lista_servico;
    private string $codigo_cnae;
    private string $codigo_tributario_municipio;
    private string $discriminacao;
    private string $codigo_municipio;
    private bool $iss_retido;
    private array $valores;

    public function __construct(
        string $item_lista_servico,
        string $codigo_cnae,
        string $codigo_tributario_municipio,
        string $discriminacao,
        string $codigo_municipio,
        bool $iss_retido,
        array $valores
    ) {
        $this->item_lista_servico = $item_lista_servico;
        $this->codigo_cnae = $codigo_cnae;
        $this->codigo_tributario_municipio = $codigo_tributario_municipio;
        $this->discriminacao = $discriminacao;
        $this->codigo_municipio = $codigo_municipio;
        $this->iss_retido = $iss_retido;
        $this->valores = $valores;
    }

    public function toArray(): array
    {
        $data = [
            'item_lista_servico' => $this->item_lista_servico,
            'codigo_cnae' => $this->codigo_cnae,
            'codigo_tributario_municipio' => $this->codigo_tributario_municipio,
            'discriminacao' => $this->discriminacao,
            'iss_retido' => $this->iss_retido,
            'codigo_municipio' => $this->codigo_municipio
        ];

        foreach ($this->valores as $i => $val) {
            if(!empty($val) && $val > 0) {
                $data[$i] = $val;
            }
        }

        return $data;
    }
}