<?php

namespace Explt13\Nosmi\Exceptions;


class DatabaseConnectionException extends BaseException
{
    protected const EXC_CODE = 2000;

    protected function getDefaultMessage(array $context): string
    {
        return "Cannot establish connection with the database.";
    }
}