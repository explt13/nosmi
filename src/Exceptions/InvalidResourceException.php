<?php
namespace Explt13\Nosmi\Exceptions;

class InvalidResourceException extends \LogicException
{
    public function __construct(string $got_resource, string $expected_resource)
    {
        parent::__construct($this->getDefaultMessage($got_resource, $expected_resource));
    }

    private function getDefaultMessage(string $got_resource, string $expected_resource): string
    {
        return "Invalid resource, got: $got_resource, expected: $expected_resource";
    }
}