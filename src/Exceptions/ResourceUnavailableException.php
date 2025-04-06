<?php

namespace Explt13\Nosmi\Exceptions;

class ResourceUnavailableException extends BaseException
{
    protected const EXC_CODE = 1120;

    /**
     * @param string $resource a path to the resource
     * @param ?string $message an exception message, if not set a default message will be provided
     */
    public function __construct(
        string $resource = self::CONTEXT_NOT_SET,
        ?string $message = null
    )
    {
        parent::__construct($message, compact('resource'));
    }

    protected function getDefaultMessage(array $context): string
    {
        return sprintf("Resource is unavailable: %s", $context['resource']);
    }
}