<?php

namespace NFSe\Exceptions;

class NFSeException extends \Exception
{
    protected array $erros = [];

    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null, array $erros = [])
    {
        parent::__construct($message, $code, $previous);
        $this->erros = $erros;
    }

    public function getErros(): array
    {
        return $this->erros;
    }

    public function setErros(array $erros): void
    {
        $this->erros = $erros;
    }

    public function addErro(string $codigo, string $mensagem, string $correcao = ''): void
    {
        $this->erros[] = [
            'codigo' => $codigo,
            'mensagem' => $mensagem,
            'correcao' => $correcao,
        ];
    }
}