<?php

namespace Explt13\Nosmi\Exceptions;

class NotInArrayException extends BaseException
{
    public function __construct(
        mixed $value = self::CONTEXT_NOT_SET,
        array $array = [self::CONTEXT_NOT_SET],
        ?string $message = null)
    {
        parent::__construct($message ?? $this->getDefaultMessage(compact('value', 'array')));
    }

    protected function getDefaultMessage(array $context = []): string
    {
        return sprintf("Value %s is not in array: [%s]",
            $context['value'],
            implode(", ", $context['array'])
        );
    }
}