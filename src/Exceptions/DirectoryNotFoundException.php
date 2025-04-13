<?php

namespace Explt13\Nosmi\Exceptions;

class DirectoryNotFoundException extends ResourceNotFoundException
{
    protected const EXC_CODE = 1111;

    protected function getDefaultMessage(array $context = []): string
    {
        return sprintf("Cannot find the directory: %s", $context['resource']);
    }
}