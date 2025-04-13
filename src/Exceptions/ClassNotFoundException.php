<?php

namespace Explt13\Nosmi\Exceptions;

class ClassNotFoundException extends BaseException
{
    protected const EXC_CODE = 1080;
    
    public function __construct(
        string $class_name = self::CONTEXT_NOT_SET,
        ?string $message = null
    )
    {
        parent::__construct($message ?? $this->getDefaultMessage(compact('class_name')));
    }

    protected function getDefaultMessage(array $context = []): string
    {
        return sprintf("Class or interface `%s` is not found.",
            $context['class_name']
        );
    }
}