<?php

declare(strict_types=1);

namespace FocusNFe\Http;

use FocusNFe\Config\Config;
use FocusNFe\Exception\FocusNFeException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class HttpClient
{
    private Client $client;
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $config->getApiUrl(),
            'timeout' => $config->getTimeout(),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'auth' => [$config->getToken(), ''],
        ]);
    }

    /**
     * @throws FocusNFeException
     */
    public function get(string $uri, array $query = []): array
    {
        try {
            $response = $this->client->get($this->config->getApiUrl() . $uri, ['query' => $query]);
            return $this->handleResponse($response);
        } catch (GuzzleException $e) {
            throw $this->handleException($e);
        }
    }

    /**
     * @throws FocusNFeException
     */
    public function post(string $uri, array $data = []): array
    {
        try {
            $response = $this->client->post($this->config->getApiUrl() . $uri, ['json' => $data]);
            return $this->handleResponse($response);
        } catch (ClientException $ex) {
            print_r($ex->getResponse()->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw $this->handleException($e);
        }
    }

    /**
     * @throws FocusNFeException
     */
    public function put(string $uri, array $data = []): array
    {
        try {
            $response = $this->client->put($this->config->getApiUrl() . $uri, ['json' => $data]);
            return $this->handleResponse($response);
        } catch (GuzzleException $e) {
            throw $this->handleException($e);
        }
    }

    /**
     * @throws FocusNFeException
     */
    public function delete(string $uri): array
    {
        try {
            $response = $this->client->delete($this->config->getApiUrl() . $uri);
            return $this->handleResponse($response);
        } catch (GuzzleException $e) {
            throw $this->handleException($e);
        }
    }

    private function handleResponse(ResponseInterface $response): array
    {
        $content = $response->getBody()->getContents();

        if (empty($content)) {
            return [];
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new FocusNFeException('Invalid JSON response: ' . json_last_error_msg());
        }

        return $data;
    }

    private function handleException(GuzzleException $e): FocusNFeException
    {
        if ($e instanceof RequestException && $e->hasResponse()) {
            $response = $e->getResponse();
            $content = $response->getBody()->getContents();
            $data = json_decode($content, true);

            $errors = [];
            if (is_array($data) && isset($data['errors'])) {
                $errors = $data['errors'];
            }

            return new FocusNFeException(
                $data['message'] ?? $e->getMessage(),
                $response->getStatusCode(),
                $e,
                $errors
            );
        }

        return new FocusNFeException($e->getMessage(), $e->getCode(), $e);
    }
}