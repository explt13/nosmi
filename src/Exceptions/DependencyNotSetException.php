<?php
namespace Explt13\Nosmi\Exceptions;

class DependencyNotSetException extends BaseException
{
    protected const EXC_CODE = 1070;

    public function __construct(
        string $abstract = self::CONTEXT_NOT_SET,
        ?string $message = null
    )
    {
        parent::__construct($message ?? $this->getDefaultMessage(compact('abstract')));
    }

    protected function getDefaultMessage(array $context = []): string
    {
        return sprintf("No binding found for `%s`", $context['abstract']);
    }
}