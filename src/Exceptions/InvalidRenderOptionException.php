<?php

namespace Explt13\Nosmi\Exceptions;

class InvalidRenderOptionException extends ViewRenderException
{
    public function __construct(string $view = self::CONTEXT_NOT_SET, int $option = self::CONTEXT_NOT_SET, ?string $message = null)
    {
        parent::__construct($message ?? $this->getDefaultMessage(compact('view', 'option')));
    }

    protected function getDefaultMessage(array $context = []): string
    {
        return "Invalid render option '{$context['option']}' for view '{$context['view']}'";
    }
}
