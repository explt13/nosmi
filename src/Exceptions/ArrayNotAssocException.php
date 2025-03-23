<?php

namespace Explt13\Nosmi\Exceptions;

class ArrayNotAssocException extends \InvalidArgumentException
{
    public function __construct(?string $message = null, int $code = 1005)
    {
        parent::__construct($message ?? $this->getDefaultMessage(), $code);
    }

    private function getDefaultMessage(): string
    {
        return "Please provide an associative array";
    }
}