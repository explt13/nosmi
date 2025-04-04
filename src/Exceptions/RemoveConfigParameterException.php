<?php
namespace Explt13\Nosmi\Exceptions;

class RemoveConfigParameterException extends BaseException
{
    protected const EXC_CODE = 1130;

    public function __construct(string $name, string $reason)
    {
        parent::__construct(sprintf('Failed to remove config parameter "%s": %s', $name, $reason));
    }
}