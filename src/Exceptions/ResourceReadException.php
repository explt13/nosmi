<?php

namespace Explt13\Nosmi\Exceptions;

class ResourceReadException extends ResourceUnavailableException
{
    protected const EXC_CODE = 1121;

    protected function getDefaultMessage(): string
    {
        return sprintf('Cannot read a resource: %s', $this->resource);
    }
}