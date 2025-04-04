<?php
namespace Explt13\Nosmi\Exceptions;

class InvalidResourceException extends BaseException
{
    protected const EXC_CODE = 1100;

    public function __construct(?string $got_resource, ?string $expected_resource)
    {
        parent::__construct(sprintf("Invalid resource, got: %s, expected: %s",
                $got_resource,
                $expected_resource
        ));
    }
}