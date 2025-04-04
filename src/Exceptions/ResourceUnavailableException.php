<?php

namespace Explt13\Nosmi\Exceptions;

class ResourceUnavailableException extends BaseException
{
    protected const EXC_CODE = 1120;
    protected readonly string $resource;

    /**
     * @param string $resource a path to the resource
     * @param ?string $message an exception message, if not set a default message will be provided
     */
    public function __construct(string $resource, ?string $message = null)
    {
        $this->resource = $resource;
        parent::__construct($message);
    }

    protected function getDefaultMessage(): string
    {
        return sprintf("Resource is unavailable: %s", $this->resource);
    }
}