<?php

namespace Explt13\Nosmi\Exceptions;

class ArrayNotAssocException extends BaseException
{
    protected const EXC_CODE = 1130;
    
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? $this->getDefaultMessage());
    }

    protected function getDefaultMessage(array $context = []): string
    {
        return "Please provide an associative array";
    }
}