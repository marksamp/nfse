# FocusNFe - Cliente PHP para NFSe

Uma biblioteca PHP moderna para consumir a API de Nota Fiscal Eletrônica de Serviço da FocusNFe.

## Características

- **PHP 8.0+**: Utiliza recursos modernos do PHP como tipos union, match expressions e mais
- **PSR-4**: Autoload padrão com namespace bem estruturado
- **Guzzle HTTP**: Cliente HTTP robusto para comunicação com a API
- **Tratamento de Erros**: Exceções customizadas com informações detalhadas
- **Sandbox**: Suporte completo ao ambiente de homologação
- **Tipagem Forte**: Todos os métodos utilizam type hints para melhor IDE support

## Instalação

```bash
composer require marksamp/nfse
```

## Configuração

### Básica

```php
<?php
require_once 'vendor/autoload.php';

use FocusNFe\NFSeClient;

// Produção
$client = new NFSeClient('SEU_TOKEN_AQUI');

// Sandbox/Homologação
$client = new NFSeClient('SEU_TOKEN_AQUI', true);

// Com timeout personalizado
$client = new NFSeClient('SEU_TOKEN_AQUI', false, 60);
```

## Uso

### Emitindo uma NFSe

```php
<?php
use FocusNFe\NFSeClient;
use FocusNFe\Data\NFSeData;
use FocusNFe\Data\PrestadorData;
use FocusNFe\Data\TomadorData;
use FocusNFe\Data\EnderecoData;
use FocusNFe\Data\ServicoData;
use FocusNFe\Exception\FocusNFeException;

$client = new NFSeClient('SEU_TOKEN', true);

try {
    // Dados do prestador
    $prestador = new PrestadorData(
        '12345678000123',
        '123456',
        '3550308'
    );

    // Endereço do tomador
    $endereco = new EnderecoData(
        'Rua das Flores',
        '123',
        'Centro',
        '3550308',
        'SP',
        '01234567'
    );

    // Dados do tomador
    $tomador = new TomadorData(
        '98.765.432/0001-98', //CNPJ ou CPF do Tomador
        'Cliente Exemplo Ltda',
        'contato@cliente.com.br',
        $endereco
    );

    // Dados do serviço
    $servico = new ServicoData(
        '9999', // Item da lista de serviços na tabela do CNAE
        '999999', // Código CNAE
        '9999999999', // Código de tributação do município
        'DISCRIMINAÇÃO DO SERVIÇO', //Campo de observação do lançamento
        '3550308', // Código do município do serviço
        false, //ISS retido
        [
            'valor_servicos' => 1000.00,
            'valor_deducoes' => 0.00,
            'valor_pis' => 0.00,
            'valor_cofins' => 0.00,
            'valor_inss' => 0.00,
            'valor_ir' => 0.00,
            'valor_csll' => 0.00,
            'valor_iss' => 0.00,
            'valor_iss_retido' => 0.00,
            'outras_retencoes' => 0.00,
            'base_calculo' => 0.00,
            'aliquota' => 0.00,
            'desconto_incondicionado' => 0.00,
            'desconto_condicionado' => 0.00,
            'percentual_total_tributos' => 0.00,
            'fonte_total_tributos' => 0.00, // IBPT
        ]
    );

    // Criar NFSe
    $nfse = new NFSeData(
        (new DateTime())->format('Y-m-d\TH:i:s'), // Código de referência próprio da nota fiscal
        '1', // Natureza da operação (01 - 06 é o padrão, mas verificar se o seu município adota outro padrão).
        '1', // Regime especial de tributação (Microempresa municipal)
        false, // Optante do Simples Nacional
        false, // Incentivador cultural
        $prestador,
        $tomador,
        $servico
    );

    // Emitir NFSe
    $referencia = time();
    $resultado = $client->nfse()->emitir($referencia, $nfse);
    
    echo "NFSe emitida com sucesso!<br>";
    echo "Referência: " . $referencia . "<br>";
    echo "Status: " . $resultado['status'] . "<br>";

    if (isset($resultado['numero'])) {
        echo "Número: " . $resultado['numero'] . "<br>";
    }

    // Consultar NFSe
    $consulta = $client->nfse()->consultar($referencia);
    echo "Consulta realizada com sucesso!<br>";
    
} catch (FocusNFeException $e) {
    echo "Erro: " . $e->getMessage();
    print_r($e->getErrors());
}
```

### Consultando uma NFSe

```php
<?php
try {
    $nfse = $client->nfse()->consultar($referencia);
    echo "Consulta realizada com sucesso!<br>";
    echo "Status: " . $nfse['status'];
    echo "Número: " . $nfse['numero'];
} catch (FocusNFeException $e) {
    echo "Erro: " . $e->getMessage();
}
```

### Acesso ao XML/PDF

```php
<?php
try {
    // Consultar NFSe
    $referencia = $_GET['ref'];
    $consulta = $client->nfse()->consultar($referencia);
    echo "Consulta realizada com sucesso!<br>";
    print_r($consulta);

    $enderecos = $client->nfse()->getDocs($referencia);
    
// Retorno:
//    Array
//(
//    [xml] => 'link completo para o xml, junto à focus'
//    [pdf] => 'link completo para o PDF, junto à focus'
//    [pdf_prefeitura] => 'link completo para o pdf junto à prefeitura, se houver.'
//)
    
} catch (FocusNFeException $e) {
    echo "Erro: " . $e->getMessage();
}
```

### Cancelando uma NFSe

```php
<?php
try {
    $resultado = $client->nfse()->cancelar(
        'referencia_da_nfse',
        'Motivo do cancelamento'
    );
    
    echo "NFSe cancelada com sucesso";
} catch (FocusNFeException $e) {
    echo "Erro: " . $e->getMessage();
}
```

### Enviando por Email

```php
<?php
try {
    $emails = ['cliente@exemplo.com.br', 'contabilidade@empresa.com.br'];
    $client->nfse()->enviarPorEmail('referencia_da_nfse', $emails);
    
    echo "Email enviado com sucesso";
} catch (FocusNFeException $e) {
    echo "Erro: " . $e->getMessage();
}
```

## Estrutura do Projeto

```
src/
├── Config/
│   └── Config.php              # Configurações da API
├── Data/
│   ├── NFSeData.php           # Estrutura principal da NFSe
│   ├── PrestadorData.php      # Dados do prestador
│   ├── TomadorData.php        # Dados do tomador
│   ├── EnderecoData.php       # Dados de endereço
│   └── ServicoData.php        # Dados do serviço
├── Exception/
│   └── FocusNFeException.php  # Exceções customizadas
├── Http/
│   └── HttpClient.php         # Cliente HTTP
├── Service/
│   └── NFSeService.php        # Serviços da NFSe
└── NFSeClient.php             # Cliente principal
```

## Métodos Disponíveis

### NFSeService

- `emitir(string $ref, NFSeData $nfse): array` - Emite uma NFSe
- `consultar(string $ref): array` - Consulta uma NFSe por referência
- `consultarPorId(string $id): array` - Consulta uma NFSe por ID
- `cancelar(string $ref, string $motivo): array` - Cancela uma NFSe
- `consultarStatus(string $ref): array` - Consulta o status de uma NFSe
- `baixarXml(string $ref): array` - Baixa o XML da NFSe
- `baixarPdf(string $ref): array` - Baixa o PDF da NFSe
- `enviarPorEmail(string $ref, array $emails): array` - Envia NFSe por email
- `listarPorPeriodo(string $dataInicial, string $dataFinal): array` - Lista NFSes por período
- `consultarEmpresa(): array` - Consulta informações da empresa

## Tratamento de Erros

A biblioteca utiliza exceções customizadas para diferentes tipos de erro:

```php
<?php
try {
    $resultado = $client->nfse()->emitir($ref, $nfse);
} catch (FocusNFeException $e) {
    echo "Código do erro: " . $e->getCode();
    echo "Mensagem: " . $e->getMessage();
    
    // Erros detalhados da API
    if (!empty($e->getErrors())) {
        foreach ($e->getErrors() as $error) {
            echo "Erro: " . $error;
        }
    }
}
```

## Requisitos

- PHP 8.0 ou superior
- Extensão JSON
- Guzzle HTTP 7.0+

## Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo LICENSE para detalhes.

## Suporte

Para suporte, abra uma issue no GitHub ou consulte a documentação oficial da FocusNFe em https://focusnfe.com.br/doc/