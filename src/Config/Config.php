<?php

declare(strict_types=1);

namespace FocusNFe\Config;

class Config
{
    private const BASE_URL_PRODUCTION = 'https://api.focusnfe.com.br';
    private const BASE_URL_SANDBOX = 'https://homologacao.focusnfe.com.br';

    private string $token;
    private bool $sandbox;
    private int $timeout;

    public function __construct(
        string $token,
        bool $sandbox = false,
        int $timeout = 30
    ) {
        $this->token = $token;
        $this->sandbox = $sandbox;
        $this->timeout = $timeout;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getBaseUrl(): string
    {
        return $this->sandbox ? self::BASE_URL_SANDBOX : self::BASE_URL_PRODUCTION;
    }

    public function getApiUrl(): string
    {
        return $this->getBaseUrl() . '/v2';
    }
}