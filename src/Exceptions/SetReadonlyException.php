<?php

namespace Explt13\Nosmi\Exceptions;

class SetReadonlyException extends BaseException
{
    protected const EXC_CODE = 1160;

    public function __construct(
        string $parameter = self::CONTEXT_NOT_SET,
        ?string $message = null
    )
    {
        parent::__construct($message, compact('parameter'));
    }

    protected function getDefaultMessage(array $context): string
    {
        return sprintf("Cannot set/modify a read-only parameter: %s", $context['parameter']);
    }
}