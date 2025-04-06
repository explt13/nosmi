<?php

namespace Explt13\Nosmi\Exceptions;

class DirectoryNotFoundException extends ResourceNotFoundException
{
    protected const EXC_CODE = 1111;
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }
}