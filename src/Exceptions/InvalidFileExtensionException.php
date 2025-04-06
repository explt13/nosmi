<?php

namespace Explt13\Nosmi\Exceptions;

class InvalidFileExtensionException extends BaseException
{
    protected const EXC_CODE = 1150;

    public function __construct(
        array $allowed_extensions = [],
        ?string $message = null
    )
    {
        parent::__construct($message, compact('allowed_extensions'));
    }

    protected function getDefaultMessage(array $context): string
    {
        return sprintf(
            "Invalid file extension. %s",
            $this->availableExtensions($context['allowed_extensions'])
        );
    }

    private function availableExtensions(array $allowed_extensions): string
    {
        if (!empty($allowed_extensions)) {
            return sprintf(
                'Supported file extensions are: [%s]',
                implode(', ', $allowed_extensions)
            );
        }
        return "";
    }
}