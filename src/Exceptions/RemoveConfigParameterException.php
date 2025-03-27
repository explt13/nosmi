<?php
namespace Explt13\Nosmi\Exceptions;

class RemoveConfigParameterException extends \LogicException
{
    public function __construct(string $name, string $reason)
    {
        parent::__construct($this->getDefaultMessage($name, $reason), 1007);
    }
    
    private function getDefaultMessage(string $name, string $reason): string
    {
        return sprintf('Failed to remove config parameter "%s": %s', $name, $reason);
    }
}