<?php

namespace Explt13\Nosmi\Exceptions;

class ViewRenderException extends BaseException
{
    protected const EXC_CODE = 3000;

    public function __construct(string $view, string $message)
    {
        parent::__construct($message ?? $this->getDefaultMessage(compact("view")));
    }
    protected function getDefaultMessage(array $context = []): string
    {
        return "Cannot render view {$context['view']}";
    }
}