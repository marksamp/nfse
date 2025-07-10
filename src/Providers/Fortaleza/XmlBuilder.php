<?php

namespace NFSe\Providers\Fortaleza;

use NFSe\Entities\NotaFiscal;

class XmlBuilder
{
    public function buildRps(NotaFiscal $notaFiscal): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<GerarNfseEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">';
        $xml .= '<Rps Id="rps_' . $notaFiscal->getNumeroRps() . '">';

        // Informações do RPS
        $xml .= '<InfRps>';
        $xml .= '<IdentificacaoRps>';
        $xml .= '<Numero>' . $notaFiscal->getNumeroRps() . '</Numero>';
        $xml .= '<Serie>' . $notaFiscal->getSerieRps() . '</Serie>';
        $xml .= '<Tipo>' . $notaFiscal->getTipoRps() . '</Tipo>';
        $xml .= '</IdentificacaoRps>';
        $xml .= '<DataEmissao>' . $notaFiscal->getDataEmissao()->format('Y-m-d\TH:i:s') . '</DataEmissao>';
        $xml .= '<NaturezaOperacao>' . $notaFiscal->getNaturezaOperacao() . '</NaturezaOperacao>';
        $xml .= '<RegimeEspecialTributacao>' . $notaFiscal->getRegimeEspecialTributacao() . '</RegimeEspecialTributacao>';
        $xml .= '<OptanteSimplesNacional>' . ($notaFiscal->isOptanteSimplesNacional() ? 1 : 2) . '</OptanteSimplesNacional>';
        $xml .= '<IncentivadorCultural>' . ($notaFiscal->isIncentivadorCultural() ? 1 : 2) . '</IncentivadorCultural>';
        $xml .= '<Status>' . $notaFiscal->getStatus() . '</Status>';

        // Prestador
        $xml .= '<Prestador>';
        $xml .= '<Cnpj>' . $notaFiscal->getPrestadorCnpj() . '</Cnpj>';
        $xml .= '<InscricaoMunicipal>' . $notaFiscal->getPrestadorInscricaoMunicipal() . '</InscricaoMunicipal>';
        $xml .= '</Prestador>';

        // Tomador
        $xml .= '<Tomador>';
        $xml .= '<IdentificacaoTomador>';
        $xml .= '<CpfCnpj>';
        $xml .= '<Cnpj>' . $notaFiscal->getTomadorCnpj() . '</Cnpj>';
        $xml .= '</CpfCnpj>';
        $xml .= '</IdentificacaoTomador>';
        $xml .= '<RazaoSocial>' . htmlspecialchars($notaFiscal->getTomadorRazaoSocial()) . '</RazaoSocial>';
        $xml .= '<Endereco>';
        $xml .= '<Endereco>' . htmlspecialchars($notaFiscal->getTomadorEndereco()) . '</Endereco>';
        $xml .= '<Numero>' . $notaFiscal->getTomadorNumero() . '</Numero>';
        if ($notaFiscal->getTomadorComplemento()) {
            $xml .= '<Complemento>' . htmlspecialchars($notaFiscal->getTomadorComplemento()) . '</Complemento>';
        }
        $xml .= '<Bairro>' . htmlspecialchars($notaFiscal->getTomadorBairro()) . '</Bairro>';
        $xml .= '<CodigoMunicipio>' . $notaFiscal->getTomadorCodigoMunicipio() . '</CodigoMunicipio>';
        $xml .= '<Uf>' . $notaFiscal->getTomadorUf() . '</Uf>';
        $xml .= '<Cep>' . $notaFiscal->getTomadorCep() . '</Cep>';
        $xml .= '</Endereco>';
        if ($notaFiscal->getTomadorTelefone()) {
            $xml .= '<Contato>';
            $xml .= '<Telefone>' . $notaFiscal->getTomadorTelefone() . '</Telefone>';
            $xml .= '<Email>' . $notaFiscal->getTomadorEmail() . '</Email>';
            $xml .= '</Contato>';
        }
        $xml .= '</Tomador>';

        // Serviços
        $xml .= '<Servico>';
        $xml .= '<Valores>';
        $xml .= '<ValorServicos>' . number_format($notaFiscal->getValorServicos(), 2, '.', '') . '</ValorServicos>';
        $xml .= '<ValorDeducoes>' . number_format($notaFiscal->getValorDeducoes(), 2, '.', '') . '</ValorDeducoes>';
        $xml .= '<ValorPis>' . number_format($notaFiscal->getValorPis(), 2, '.', '') . '</ValorPis>';
        $xml .= '<ValorCofins>' . number_format($notaFiscal->getValorCofins(), 2, '.', '') . '</ValorCofins>';
        $xml .= '<ValorInss>' . number_format($notaFiscal->getValorInss(), 2, '.', '') . '</ValorInss>';
        $xml .= '<ValorIr>' . number_format($notaFiscal->getValorIr(), 2, '.', '') . '</ValorIr>';
        $xml .= '<ValorCsll>' . number_format($notaFiscal->getValorCsll(), 2, '.', '') . '</ValorCsll>';
        $xml .= '<IssRetido>' . ($notaFiscal->getIssRetido() > 0 ? 1 : 2) . '</IssRetido>';
        $xml .= '<ValorIss>' . number_format($notaFiscal->getValorIss(), 2, '.', '') . '</ValorIss>';
        $xml .= '<Aliquota>' . number_format($notaFiscal->getAliquota(), 4, '.', '') . '</Aliquota>';
        $xml .= '<DescontoIncondicionado>' . number_format($notaFiscal->getDescontoIncondicionado(), 2, '.', '') . '</DescontoIncondicionado>';
        $xml .= '<DescontoCondicionado>' . number_format($notaFiscal->getDescontoCondicionado(), 2, '.', '') . '</DescontoCondicionado>';
        $xml .= '</Valores>';
        $xml .= '<ItemListaServico>' . $notaFiscal->getItemListaServico() . '</ItemListaServico>';
        if ($notaFiscal->getCodigoCnae()) {
            $xml .= '<CodigoCnae>' . $notaFiscal->getCodigoCnae() . '</CodigoCnae>';
        }
        if ($notaFiscal->getCodigoTributacaoMunicipio()) {
            $xml .= '<CodigoTributacaoMunicipio>' . $notaFiscal->getCodigoTributacaoMunicipio() . '</CodigoTributacaoMunicipio>';
        }
        $xml .= '<Discriminacao>' . htmlspecialchars($notaFiscal->getDiscriminacao()) . '</Discriminacao>';
        $xml .= '<MunicipioIbge>' . $notaFiscal->getCodigoMunicipio() . '</MunicipioIbge>';
        $xml .= '</Servico>';

        $xml .= '</InfRps>';
        $xml .= '</Rps>';
        $xml .= '</GerarNfseEnvio>';

        return $xml;
    }

    public function buildCancelamento(string $numeroNota, string $motivo): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<CancelarNfseEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">';
        $xml .= '<Pedido>';
        $xml .= '<InfPedidoCancelamento>';
        $xml .= '<IdentificacaoNfse>';
        $xml .= '<Numero>' . $numeroNota . '</Numero>';
        $xml .= '</IdentificacaoNfse>';
        $xml .= '<CodigoCancelamento>1</CodigoCancelamento>';
        $xml .= '<MotivoCancelamento>' . htmlspecialchars($motivo) . '</MotivoCancelamento>';
        $xml .= '</InfPedidoCancelamento>';
        $xml .= '</Pedido>';
        $xml .= '</CancelarNfseEnvio>';

        return $xml;
    }

    public function buildConsultaNota(string $numeroNota): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<ConsultarNfseEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">';
        $xml .= '<PedidoConsultaNfse>';
        $xml .= '<IdentificacaoNfse>';
        $xml .= '<Numero>' . $numeroNota . '</Numero>';
        $xml .= '</IdentificacaoNfse>';
        $xml .= '</PedidoConsultaNfse>';
        $xml .= '</ConsultarNfseEnvio>';

        return $xml;
    }

    public function buildConsultaLoteRps(string $numeroLote): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<ConsultarLoteRpsEnvio xmlns="http://www.abrasf.org.br/nfse.xsd">';
        $xml .= '<PedidoConsultaLoteRps>';
        $xml .= '<Protocolo>' . $numeroLote . '</Protocolo>';
        $xml .= '</PedidoConsultaLoteRps>';
        $xml .= '</ConsultarLoteRpsEnvio>';

        return $xml;
    }
}