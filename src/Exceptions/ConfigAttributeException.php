<?php

namespace Explt13\Nosmi\Exceptions;

class ConfigAttributeException extends BaseException
{
    protected const EXC_CODE = 1131;

    /**
     * Construct the exception. Note: The message is NOT binary safe.\
     * If $expected_value and $got_value are not provided, the "missing attribute message" will be provided
     * @param ?string $parameter_name The name of the config parameter
     * @param ?string $attribute_name The name of the problematic attribute
     * @param ?string $expected_value The expected value for the attribute
     * @param ?string $got_value The actual value provided for the attribute
     */
    public function __construct(
        string $parameter_name = self::CONTEXT_NOT_SET,
        string $attribute_name = self::CONTEXT_NOT_SET,
        string $expected_value = self::CONTEXT_NOT_SET,
        string $got_value = self::CONTEXT_NOT_SET,
        ?string $message = null,
    )
    {
        parent::__construct($message, compact('parameter_name', 'attribute_name', 'expected_value', 'got_value'));
    }

    protected function getDefaultMessage(array $context): string
    {
        if (is_null($context['expected_value']) && is_null($context['got_value'])) {
            return sprintf("Cannot set the `%s` parameter: missing the `%s` attribute",
                    $context['parameter_name'], 
                    $context['attribute_name']
            );
        }
        return sprintf("Cannot set the `%s` parameter: expected the `%s` attribute to have value `%s` but got `%s`",
                $context['parameter_name'],
                $context['attribute_name'],
                $context['expected_value'],
                $context['got_value']
        );
    }
}