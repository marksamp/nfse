<?php

declare(strict_types=1);

namespace FocusNFe\Service;

use FocusNFe\Data\NFSeData;
use FocusNFe\Exception\FocusNFeException;
use FocusNFe\Http\HttpClient;

class NFSeService
{
    private HttpClient $httpClient;
    private string $url_xml = 'https://api.focusnfe.com.br';

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Emite uma NFSe
     *
     * @throws FocusNFeException
     */
    public function emitir(string $ref, NFSeData $nfse): array
    {
        return $this->httpClient->post("/nfse?ref={$ref}", $nfse->toArray());
    }

    /**
     * Consulta uma NFSe
     *
     * @throws FocusNFeException
     */
    public function consultar(string $ref): array
    {
        return $this->httpClient->get("/nfse/{$ref}");
    }

    /**
     * Cancela uma NFSe
     *
     * @throws FocusNFeException
     */
    public function cancelar(string $ref, string $motivo): array
    {
        return $this->httpClient->delete("/nfse/{$ref}", [
            'justificativa' => $motivo
        ]);
    }

    /**
     * Coleta o link para baixar o PDF e o XML da NFSe
     *
     * @throws FocusNFeException
     */
    public function getDocs(string $ref): array
    {
        $consulta = $this->consultar($ref);
        if(!in_array('erros', $consulta))
            return ['xml' => $this->url_xml . $consulta['caminho_xml_nota_fiscal'], 'pdf' => $consulta['url_danfse'], 'pdf_prefeitura' => $consulta['url']];

        return [];
    }

    /**
     * Envia uma NFSe por email
     *
     * @throws FocusNFeException
     */
    public function enviarPorEmail(string $ref, array $emails): array
    {
        return $this->httpClient->post("/nfse/{$ref}/email", [
            'emails' => $emails
        ]);
    }

    /**
     * Consulta informações sobre a empresa
     *
     * @throws FocusNFeException
     */
    public function consultarEmpresa(): array
    {
        return $this->httpClient->get('/empresas');
    }
}