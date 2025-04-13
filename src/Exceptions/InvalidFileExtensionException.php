<?php

namespace Explt13\Nosmi\Exceptions;

class InvalidFileExtensionException extends BaseException
{
    protected const EXC_CODE = 1150;

    public function __construct(
        string $for_file = self::CONTEXT_NOT_SET,
        array $allowed_extensions = [],
        ?string $message = null
    )
    {
        parent::__construct($message ?? $this->getDefaultMessage(compact('for_file', 'allowed_extensions')));
    }

    protected function getDefaultMessage(array $context = []): string
    {
        return sprintf(
            "File `%s` has invalid extension. %s",
            $context['for_file'],
            $this->availableExtensions($context['allowed_extensions'])
        );
    }

    private function availableExtensions(array $allowed_extensions): string
    {
        if (!empty($allowed_extensions)) {
            return sprintf(
                'The supported file extensions are: [%s]',
                implode(', ', $allowed_extensions)
            );
        }
        return "";
    }
}