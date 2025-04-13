<?php

namespace Explt13\Nosmi\Exceptions;

abstract class BaseException extends \Exception
{
    protected const EXC_CODE = 1000;
    protected const CONTEXT_NOT_SET = '__CONTEXT_NOT_SET__';

    public function __construct(?string $message = null)
    {
        parent::__construct($message, static::EXC_CODE);
    }

    abstract protected function getDefaultMessage(array $context = []): string;

    public final static function withMessage(string $custom_message): static
    {
        return new static(message: $custom_message);
    }
}