<?php

namespace Explt13\Nosmi\Exceptions;

class InvalidAssocArrayValueException extends BaseException
{
    protected const EXC_CODE = 1132;

    /**
     * Construct the exception.
     * @param string $name The name of the variable or array key that has an assoc array as its value
     * @param string $key_name The name of the problematic key
     * @param string $expected_value The expected value for the key
     * @param string $got_value The actual value provided for the key
     * @param ?string $message Optional custom message, __should not__ be set via constructor if a custom message needed, use: ::withMessage() method
     */
    public function __construct(
        string $name = self::CONTEXT_NOT_SET,
        string $key_name = self::CONTEXT_NOT_SET,
        string $expected_value = self::CONTEXT_NOT_SET,
        string $got_value = self::CONTEXT_NOT_SET,
        ?string $message = null,
    )
    {
        parent::__construct($message ?? $this->getDefaultMessage(compact('name', 'key_name', 'expected_value', 'got_value')));
    }

    protected function getDefaultMessage(array $context = []): string
    {
        return sprintf(
            "Cannot set the `%s` parameter: expected the `%s` attribute to have value `%s` but got `%s`",
            $context['name'],
            $context['key_name'],
            $context['expected_value'],
            $context['got_value']
        );
    }
}