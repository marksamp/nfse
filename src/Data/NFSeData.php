<?php

declare(strict_types=1);

namespace FocusNFe\Data;

class NFSeData
{
    private $data_emissao;
    private string $natureza_operacao;
    private string $regime_especial_tributacao;
    private bool $optante_simples_nacional;
    private bool $incentivador_cultural;
    private string $status;
    private ?string $codigo_obra = null;
    private ?string $art = null;
    private PrestadorData $prestador;
    private TomadorData $tomador;
    private ServicoData $servico;

    public function __construct(
        string $data_emissao,
        string $natureza_operacao,
        string $regime_especial_tributacao,
        bool $optante_simples_nacional,
        bool $incentivador_cultural,
        PrestadorData $prestador,
        TomadorData $tomador,
        ServicoData $servico
    ) {
        $this->data_emissao = $data_emissao;
        $this->natureza_operacao = $natureza_operacao;
        $this->regime_especial_tributacao = $regime_especial_tributacao;
        $this->optante_simples_nacional = $optante_simples_nacional;
        $this->incentivador_cultural = $incentivador_cultural;
        $this->prestador = $prestador;
        $this->tomador = $tomador;
        $this->servico = $servico;
    }
    public function setDataEmissao(?string $data_emissao): self
    {
        $this->data_emissao = $data_emissao;
        return $this;
    }

    public function setCodigoObra(?string $codigo_obra): self
    {
        $this->codigo_obra = $codigo_obra;
        return $this;
    }

    public function setArt(?string $art): self
    {
        $this->art = $art;
        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'data_emissao' => $this->data_emissao,
            'natureza_operacao' => $this->natureza_operacao,
            'regime_especial_tributacao' => $this->regime_especial_tributacao,
            'optante_simples_nacional' => $this->optante_simples_nacional,
            'incentivador_cultural' => $this->incentivador_cultural,
            'prestador' => $this->prestador->toArray(),
            'tomador' => $this->tomador->toArray(),
            'servico' => $this->servico->toArray(),
        ];

        if ($this->codigo_obra !== null) {
            $data['codigo_obra'] = $this->codigo_obra;
        }

        if ($this->art !== null) {
            $data['art'] = $this->art;
        }

        return $data;
    }
}