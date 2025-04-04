<?php

namespace Explt13\Nosmi\Exceptions;


class ResourceNotFoundException extends BaseException
{
    protected const EXC_CODE = 1110;

    /**
     * @param string $resource a path to the resource
     * @param ?string $message an exception message, if not set the default message will be provided
     */
    public function __construct(string $resource)
    {
        parent::__construct(sprintf("Cannot find the resource: %s", $resource));
    }
}