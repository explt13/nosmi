<?php

namespace Explt13\Nosmi\Exceptions;

class InvalidTypeException extends BaseException
{
    protected const EXC_CODE = 1190;

    public function __construct(
        string $expected_type = self::CONTEXT_NOT_SET,
        string $got_type = self::CONTEXT_NOT_SET,
        ?string $message = null
    )
    {
        parent::__construct($message ?? $this->getDefaultMessage(compact('expected_type', 'got_type')));
    }

    protected function getDefaultMessage(array $context = []): string
    {
        return sprintf("Invalid type of object: expected type: %s, but got: %s", 
            $context['expected_type'],
            $context['got_type'],
        );
    }
}