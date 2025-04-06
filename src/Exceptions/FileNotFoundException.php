<?php

namespace Explt13\Nosmi\Exceptions;

class FileNotFoundException extends ResourceNotFoundException
{
    protected const EXC_CODE = 1112;
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }
}