<?php

namespace Explt13\Nosmi\Exceptions;

class SetReadonlyException extends \LogicException
{
    public function __construct(string $parameter, ?string $msg = null)
    {
        $msg = $msg ?? $this->getDefaultMessage($parameter);

        parent::__construct($msg, 1004);
    }

    /**
     * Gets a default exception message
     * @param string $parameter a paremeter that cannot be modified
     * @return string
     */

    private function getDefaultMessage(string $parameter): string
    {
        return "Cannot set/modify a read-only parameter: $parameter";
    }
}