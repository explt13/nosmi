<?php

namespace Explt13\Nosmi\Exceptions;

class ConfigParameterNotSetException extends BaseException
{
    public function __construct(string $name = self::CONTEXT_NOT_SET, ?string $message = null)
    {
        parent::__construct($message ?? $this->getDefaultMessage(compact('name')));
    }

    protected function getDefaultMessage(array $context = []): string
    {
        return "Missing config parameter: {$context['name']}";
    }
}