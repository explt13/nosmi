<?php
namespace Explt13\Nosmi\Validators;

use Explt13\Nosmi\Interfaces\FileValidatorInterface;

class FileValidator implements FileValidatorInterface
{
    public function fileExists(string $path): bool
    {
        return file_exists($path);
    }

    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    public function isReadable(string $path): bool
    {
        return is_readable($path);
    }

    public function isValidExtension(string $extension, array $extensions): bool
    {
        return in_array($extension, $extensions, true);
    }
}