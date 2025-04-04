<?php

namespace Explt13\Nosmi\Exceptions;

class InvalidFileExtensionException extends BaseException
{
    protected const EXC_CODE = 1150;

    public function __construct(array $allowed_extensions)
    {
        parent::__construct(sprintf(
            "Invalid file extension. %s",
            $this->availableExtensions($allowed_extensions)
        ));
    }

    private function availableExtensions($allowed_extensions): string|null
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