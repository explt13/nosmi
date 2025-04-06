<?php
namespace Explt13\Nosmi\Exceptions;

class InvalidResourceException extends BaseException
{
    protected const EXC_CODE = 1100;

    public function __construct(
        string $got_resource = self::CONTEXT_NOT_SET,
        string $expected_resource = self::CONTEXT_NOT_SET,
        ?string $message = null
    )
    {
        parent::__construct($message, compact('got_resource', 'expected_resource'));
    }

    protected function getDefaultMessage(array $context): string
    {
        return sprintf("Invalid resource, got: %s, expected: %s",
                $context['got_resource'],
                $context['expected_resource']
        );
    }
}