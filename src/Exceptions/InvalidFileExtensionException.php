<?php

namespace Explt13\Nosmi\Exceptions;

class InvalidFileExtensionException extends \RuntimeException
{
    public function __construct(?string $msg = null, ?array $allowed_extensions = null)
    {
        $msg = $msg ?? $this->getDefaultMessage();
        $msg .= $this->availableExtensions($allowed_extensions);
        parent::__construct($msg);
    }

    private function getDefaultMessage(): string
    {
        return "Invalid file extension, use appropriate file extension.";
    }


    private function availableExtensions($allowed_extensions): string|null
    {
        if (!is_null($allowed_extensions)) {
            return ' Supported file extension are: [' . join(', ', $allowed_extensions) . ']';
        }
        return null;
    }
}