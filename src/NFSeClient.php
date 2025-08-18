<?php

declare(strict_types=1);

namespace FocusNFe;

use FocusNFe\Config\Config;
use FocusNFe\Http\HttpClient;
use FocusNFe\Service\NFSeService;

class NFSeClient
{
    private Config $config;
    private HttpClient $httpClient;
    private NFSeService $nfseService;

    public function __construct(string $token, bool $sandbox = false, int $timeout = 30)
    {
        $this->config = new Config($token, $sandbox, $timeout);
        $this->httpClient = new HttpClient($this->config);
        $this->nfseService = new NFSeService($this->httpClient);
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    public function getNFSeService(): NFSeService
    {
        return $this->nfseService;
    }

    /**
     * Método de conveniência para acessar o serviço de NFSe
     */
    public function nfse(): NFSeService
    {
        return $this->nfseService;
    }
}