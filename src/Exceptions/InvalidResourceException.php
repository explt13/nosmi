<?php
namespace Explt13\Nosmi\Exceptions;

class InvalidResourceException extends BaseException
{
    protected const EXC_CODE = 1100;

    public function __construct(
        string $got_resource = self::CONTEXT_NOT_SET,
        string $expected_resource = self::CONTEXT_NOT_SET,
        string $for_path = self::CONTEXT_NOT_SET,
        ?string $message = null
    )
    {
        parent::__construct($message ?? $this->getDefaultMessage(compact('got_resource', 'expected_resource', 'for_path')));
    }

    protected function getDefaultMessage(array $context = []): string
    {
        return sprintf("Invalid resource for: %s, got: %s, expected: %s",
                $context['for_path'],
                $context['got_resource'],
                $context['expected_resource'],
        );
    }
}