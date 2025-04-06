<?php
namespace Explt13\Nosmi\Exceptions;

class RemoveConfigParameterException extends BaseException
{
    protected const EXC_CODE = 1130;

    public function __construct(
        string $name = self::CONTEXT_NOT_SET,
        string $reason = self::CONTEXT_NOT_SET,
        ?string $message = null
    )
    {
        parent::__construct($message, compact('name', 'reason'));
    }

    protected function getDefaultMessage(array $context): string
    {
        return sprintf('Failed to remove config parameter "%s": %s', $context['name'], $context['reason']);
    }
}