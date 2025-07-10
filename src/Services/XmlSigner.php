<?php

namespace NFSe\Services;

use NFSe\Entities\Certificado;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class XmlSigner
{
    private Certificado $certificado;

    public function __construct(Certificado $certificado)
    {
        $this->certificado = $certificado;
    }

    public function assinarXml(string $xml, string $tagAssinatura = 'Rps'): string
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);

        // Encontrar o elemento a ser assinado
        $elementos = $dom->getElementsByTagName($tagAssinatura);

        if ($elementos->length === 0) {
            throw new \InvalidArgumentException("Elemento '{$tagAssinatura}' não encontrado no XML");
        }

        foreach ($elementos as $elemento) {
            $this->assinarElemento($dom, $elemento);
        }

        return $dom->saveXML();
    }

    private function assinarElemento(\DOMDocument $dom, \DOMElement $elemento): void
    {
        // Criar o objeto de assinatura
        $objDSig = new XMLSecurityDSig();
        $objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);

        // Adicionar referência ao elemento
        $id = $elemento->getAttribute('Id');
        if (empty($id)) {
            $id = 'rps_' . uniqid();
            $elemento->setAttribute('Id', $id);
        }

        $objDSig->addReference(
            $elemento,
            XMLSecurityDSig::SHA1,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature', XMLSecurityDSig::EXC_C14N],
            ['id_name' => 'Id', 'overwrite' => false]
        );

        // Criar chave de assinatura
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, ['type' => 'private']);
        $objKey->loadKey($this->certificado->getChavePrivadaPem());

        // Assinar o documento
        $objDSig->sign($objKey);

        // Adicionar certificado
        $objDSig->add509Cert($this->certificado->getCertificadoPem());

        // Anexar assinatura ao elemento
        $objDSig->appendSignature($elemento);
    }

    public function validarAssinatura(string $xml): bool
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);

        $objDSig = new XMLSecurityDSig();
        $objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);

        $objDSig->idKeys = ['Id'];

        // Localizar assinatura
        $signature = $objDSig->locateSignature($dom);
        if (!$signature) {
            return false;
        }

        $objDSig->canonicalizeSignedInfo();

        // Validar referências
        if (!$objDSig->validateReference()) {
            return false;
        }

        // Obter chave pública
        $objKey = $objDSig->locateKey();
        if (!$objKey) {
            return false;
        }

        $objKey->loadKey($this->certificado->getCertificadoPem());

        // Validar assinatura
        return $objDSig->verify($objKey) === 1;
    }
}