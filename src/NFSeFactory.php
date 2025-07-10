<?php

namespace NFSe;

use NFSe\Contracts\NFSeInterface;
use NFSe\Providers\Fortaleza\FortalezaNFSe;

class NFSeFactory
{
    private const PROVIDERS = [
        'fortaleza' => FortalezaNFSe::class,
        // Adicionar outros municípios aqui
        // 'sao_paulo' => SaoPauloNFSe::class,
        // 'rio_de_janeiro' => RioDeJaneiroNFSe::class,
    ];

    public static function create(string $municipio): NFSeInterface
    {
        $municipio = strtolower($municipio);

        if (!isset(self::PROVIDERS[$municipio])) {
            throw new \InvalidArgumentException("Município '{$municipio}' não suportado");
        }

        $providerClass = self::PROVIDERS[$municipio];

        return new $providerClass();
    }

    public static function getMunicipiosSuportados(): array
    {
        return array_keys(self::PROVIDERS);
    }
}