<?php

namespace FocusNFe\Exception;

class FocusNFeException extends \Exception
{
    protected $errors = [];

    public function __construct($message = "", $code = 0, $errors = [], \Throwable $previous = null)
    {
        $this->errors = is_array($errors) ? $errors : [];
        parent::__construct($message, $code, $previous);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}