<?php

namespace Explt13\Nosmi\Exceptions;

abstract class BaseException extends \Exception
{
    protected const EXC_CODE = 1000;
    protected const CONTEXT_NOT_SET = '__CONTEXT_NOT_SET__';
    protected ?string $custom_message = null;
    
    public function __construct(?string $message = null, array $context = [])
    {
        parent::__construct($message ?? $this->getDefaultMessage($context), static::EXC_CODE);
    }

    protected function getDefaultMessage(array $context): string
    {
        return "An exception has occurred";
    }

    public final static function withMessage(string $custom_message): static
    {
        return new static(message: $custom_message);
    }
}