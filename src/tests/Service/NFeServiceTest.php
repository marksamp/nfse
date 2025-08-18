<?php

declare(strict_types=1);

namespace FocusNFe\Tests\Service;

use FocusNFe\Config\Config;
use FocusNFe\Data\NFSeData;
use FocusNFe\Data\PrestadorData;
use FocusNFe\Data\TomadorData;
use FocusNFe\Data\EnderecoData;
use FocusNFe\Data\ServicoData;
use FocusNFe\Exception\FocusNFeException;
use FocusNFe\Http\HttpClient;
use FocusNFe\Service\NFSeService;
use PHPUnit\Framework\TestCase;

class NFSeServiceTest extends TestCase
{
    private NFSeService $service;
    private Config $config;

    protected function setUp(): void
    {
        $this->config = new Config('test_token', true);
        $httpClient = new HttpClient($this->config);
        $this->service = new NFSeService($httpClient);
    }

    public function testCanCreateNFSeData(): void
    {
        $prestador = new PrestadorData(
            '12345678000123',
            '123456',
            '3550308'
        );

        $endereco = new EnderecoData(
            'Rua das Flores',
            '123',
            'Centro',
            '3550308',
            'SP',
            '01234567'
        );

        $tomador = new TomadorData(
            'Cliente Exemplo Ltda',
            'contato@cliente.com.br',
            $endereco
        );
        $tomador->setCnpj('98765432000198');

        $servico = new ServicoData(
            '1.01',
            '6204000',
            '01010101',
            'Desenvolvimento de software',
            '3550308',
            [
                'servicos' => 1000.00,
                'deducoes' => 0.00,
                'pis' => 0.00,
                'cofins' => 0.00,
                'inss' => 0.00,
                'ir' => 0.00,
                'csll' => 0.00,
                'iss' => 50.00,
                'outras_retencoes' => 0.00,
                'valor_liquido' => 950.00
            ]
        );

        $nfse = new NFSeData(
            '1',
            '1',
            true,
            false,
            'U',
            $prestador,
            $tomador,
            $servico
        );

        $array = $nfse->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('natureza_operacao', $array);
        $this->assertArrayHasKey('prestador', $array);
        $this->assertArrayHasKey('tomador', $array);
        $this->assertArrayHasKey('servico', $array);
        $this->assertEquals('1', $array['natureza_operacao']);
        $this->assertTrue($array['optante_simples_nacional']);
        $this->assertFalse($array['incentivador_cultural']);
    }

    public function testCanSetOptionalFields(): void
    {
        $prestador = new PrestadorData('12345678000123', '123456', '3550308');
        $endereco = new EnderecoData('Rua das Flores', '123', 'Centro', '3550308', 'SP', '01234567');
        $tomador = new TomadorData('Cliente Exemplo Ltda', 'contato@cliente.com.br', $endereco);
        $servico = new ServicoData('1.01', '6204000', '01010101', 'Desenvolvimento de software', '3550308', []);

        $nfse = new NFSeData('1', '1', true, false, 'U', $prestador, $tomador, $servico);
        $nfse->setCodigoObra('OBRA123');
        $nfse->setArt('ART456');

        $array = $nfse->toArray();

        $this->assertArrayHasKey('codigo_obra', $array);
        $this->assertArrayHasKey('art', $array);
        $this->assertEquals('OBRA123', $array['codigo_obra']);
        $this->assertEquals('ART456', $array['art']);
    }

    public function testEnderecoCanSetComplemento(): void
    {
        $endereco = new EnderecoData(
            'Rua das Flores',
            '123',
            'Centro',
            '3550308',
            'SP',
            '01234567'
        );
        $endereco->setComplemento('Sala 101');

        $array = $endereco->toArray();

        $this->assertArrayHasKey('complemento', $array);
        $this->assertEquals('Sala 101', $array['complemento']);
    }

    public function testTomadorCanSetCnpjOrCpf(): void
    {
        $endereco = new EnderecoData('Rua das Flores', '123', 'Centro', '3550308', 'SP', '01234567');
        $tomador = new TomadorData('Cliente Exemplo Ltda', 'contato@cliente.com.br', $endereco);

        // Testar CNPJ
        $tomador->setCnpj('98765432000198');
        $array = $tomador->toArray();
        $this->assertArrayHasKey('cnpj', $array);
        $this->assertArrayNotHasKey('cpf', $array);
        $this->assertEquals('98765432000198', $array['cnpj']);

        // Testar CPF (deve limpar CNPJ)
        $tomador->setCpf('12345678901');
        $array = $tomador->toArray();
        $this->assertArrayHasKey('cpf', $array);
        $this->assertArrayNotHasKey('cnpj', $array);
        $this->assertEquals('12345678901', $array['cpf']);
    }

    public function testConfigGetters(): void
    {
        $config = new Config('test_token', true, 60);

        $this->assertEquals('test_token', $config->getToken());
        $this->assertTrue($config->isSandbox());
        $this->assertEquals(60, $config->getTimeout());
        $this->assertEquals('https://homologacao.focusnfe.com.br', $config->getBaseUrl());
        $this->assertEquals('https://homologacao.focusnfe.com.br/v2', $config->getApiUrl());
    }

    public function testProductionConfig(): void
    {
        $config = new Config('prod_token', false);

        $this->assertFalse($config->isSandbox());
        $this->assertEquals('https://api.focusnfe.com.br', $config->getBaseUrl());
        $this->assertEquals('https://api.focusnfe.com.br/v2', $config->getApiUrl());
    }
}