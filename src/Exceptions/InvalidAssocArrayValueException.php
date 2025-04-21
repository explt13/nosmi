<?php

namespace Explt13\Nosmi\Exceptions;

class InvalidAssocArrayValueException extends BaseException
{
    protected const EXC_CODE = 1132;

    /**
     * Construct the exception.
     * @param string $name The name of the variable or array key that has an assoc array as its value
     * @param string $key_name The name of the problematic key
     * @param string|array $expected_value(s) The expected value for the key
     * @param string $got_value The actual value provided for the key
     * @param ?string $message Optional custom message, __should not__ be set via constructor if a custom message needed, use: ::withMessage() method instead.
     */
    public function __construct(
        string $key_name = self::CONTEXT_NOT_SET,
        string|array $expected_value = self::CONTEXT_NOT_SET,
        string $got_value = self::CONTEXT_NOT_SET,
        ?string $message = null,
    )
    {
        parent::__construct($message ?? $this->getDefaultMessage(compact('key_name', 'expected_value', 'got_value')));
    }

    protected function getDefaultMessage(array $context = []): string
    {
        $expected_value = $context['expected_value'];
        if (is_array($expected_value)) {
            $expected_value = sprintf('[%s]', implode(', ', $expected_value));
        }
        return sprintf(
            "Assoc array invalid key: expected the `%s` key to have value(s) `%s` but got `%s`",
            $context['key_name'],
            $expected_value,
            $context['got_value']
        );
    }
}