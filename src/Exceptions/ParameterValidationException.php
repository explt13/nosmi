<?php

namespace Explt13\Nosmi\Exceptions;

class ParameterValidationException extends \LogicException
{
    public function __construct($parameter_name, $attribute_name, $expected_value, $got_value)
    {
        $msg = "Cannot set a $parameter_name parameter: expected $attribute_name to have value $expected_value but got $got_value";
        
        parent::__construct($msg, 1003);
    }
}