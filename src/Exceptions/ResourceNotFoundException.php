<?php

namespace Explt13\Nosmi\Exceptions;


class ResourceNotFoundException extends BaseException
{
    protected const EXC_CODE = 1110;

    /**
     * @param string $resource a path to the resource
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
        return sprintf("Cannot find the resource: %s", $context['resource']);
    }
}