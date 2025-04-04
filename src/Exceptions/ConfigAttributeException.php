<?php

namespace Explt13\Nosmi\Exceptions;

class ConfigAttributeException extends BaseException
{
    protected const EXC_CODE = 1131;

    /**
     * Construct the exception. Note: The message is NOT binary safe.\
     * If $expected_value and $got_value are not provided, the "missing attribute message" will be provided
     * @param string $parameter_name The name of the config parameter
     * @param string $attribute_name The name of the problematic attribute
     * @param ?string [optional] $expected_value The expected value for the attribute
     * @param ?string [optional] $got_value The actual value provided for the attribute
     */
    public function __construct(string $parameter_name, string $attribute_name, ?string $expected_value = null, ?string $got_value = null)
    {
        parent::__construct(self::formatMessage($parameter_name, $attribute_name, $expected_value, $got_value));
    }

    /**
     * Formats the exception message based on the provided parameters
     * @param string $parameter_name The name of the config parameter
     * @param string $attribute_name The name of the problematic attribute
     * @param ?string [optional] $expected_value The expected value for the attribute
     * @param ?string [optional] $got_value The actual value provided for the attribute
     * @return string The formatted exception message
     */
    private static function formatMessage(string $parameter_name, string $attribute_name, ?string $expected_value, ?string $got_value): string
    {
        if (is_null($expected_value) && is_null($got_value)) {
            return sprintf("Cannot set the `%s` parameter: missing the `%s` attribute", $parameter_name, $attribute_name);
        }
        return sprintf("Cannot set the `%s` parameter: expected the `%s` attribute to have value `%s` but got `%s`",
                $parameter_name,
                $attribute_name,
                $expected_value,
                $got_value ?? '`undefined`'
        );
    }
}