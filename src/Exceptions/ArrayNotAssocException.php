<?php

namespace Explt13\Nosmi\Exceptions;

class ArrayNotAssocException extends BaseException
{
    protected const EXC_CODE = 1140;
    
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }

    protected function getDefaultMessage(array $context): string
    {
        return "Please provide an associative array";
    }
}