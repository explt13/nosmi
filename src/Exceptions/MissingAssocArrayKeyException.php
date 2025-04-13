<?php

namespace Explt13\Nosmi\Exceptions;

class MissingAssocArrayKeyException extends BaseException
{
    protected const EXC_CODE = 1131;

    /**
     * Construct the exception.
     * @param string $name The name of the variable or array key that has an assoc array as its value
     * @param string $missing_key The name of the missing key.
     * @param ?string $message Optional custom message, __should not__ be set via constructor if a custom message needed, use: ::withMessage() method instead.
     */
    public function __construct(
        string $name = self::CONTEXT_NOT_SET,
        string $missing_key = self::CONTEXT_NOT_SET,
        ?string $message = null,
    )
    {
        parent::__construct($message ?? $this->getDefaultMessage(compact('name', 'missing_key')));
    }

    protected function getDefaultMessage(array $context = []): string
    {
        return sprintf("Cannot set the `%s`: missing the `%s` key",
                $context['name'], 
                $context['missing_key']
        );
    }
}