<?php

namespace NFSe\Contracts;

use NFSe\Entities\NotaFiscal;
use NFSe\Entities\Certificado;

interface NFSeInterface
{
    public function emitir(NotaFiscal $notaFiscal): array;

    public function cancelar(string $numeroNota, string $motivo): array;

    public function consultar(string $numeroNota): array;

    public function consultarLoteRps(string $numeroLote): array;

    public function setCertificado(Certificado $certificado): void;

    public function setHomologacao(bool $homologacao): void;
}