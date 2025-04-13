<?php

namespace Explt13\Nosmi\Exceptions;


class DatabaseConnectionException extends BaseException
{
    protected const EXC_CODE = 2000;

    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? $this->getDefaultMessage());
    }

    protected function getDefaultMessage(array $context = []): string
    {
        return "Cannot establish connection with the database.";
    }
}