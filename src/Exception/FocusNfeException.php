<?php

declare(strict_types=1);

namespace FocusNFe\Exception;

use Exception;

class FocusNFeException extends Exception
{
    private array $errors;

    public function __construct(
        string $message = "",
        int $code = 0,
        ?Exception $previous = null,
        array $errors = []
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}