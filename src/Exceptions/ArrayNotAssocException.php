<?php

namespace Explt13\Nosmi\Exceptions;

class ArrayNotAssocException extends \InvalidArgumentException
{
    public function __construct(?string $message = null, int $code = 1595)
    {
        parent::__construct($message ?? "Please provide an associative array", $code);
    }
}