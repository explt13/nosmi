<?php

namespace Explt13\Nosmi\Exceptions;

class SetReadonlyException extends BaseException
{
    protected const EXC_CODE = 1160;

    public function __construct(string $parameter)
    {
        parent::__construct("Cannot set/modify a read-only parameter: $parameter");
    }
}