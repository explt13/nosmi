<?php

namespace Explt13\Nosmi\Exceptions;

class FileNotFoundException extends ResourceNotFoundException
{
    protected const EXC_CODE = 1112;

    protected function getDefaultMessage(array $context = []): string
    {
        return sprintf("Cannot find the file: %s", $context['resource']);
    }
}