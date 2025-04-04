<?php

namespace Explt13\Nosmi\Exceptions;

abstract class BaseException extends \Exception
{
    protected const EXC_CODE = 1000;
    
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? $this->getDefaultMessage(), $this::EXC_CODE);
    }

    protected function getDefaultMessage(): string
    {
        return "An exception has occured";
    }
}