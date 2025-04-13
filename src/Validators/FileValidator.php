<?php
namespace Explt13\Nosmi\Validators;

use Explt13\Nosmi\Interfaces\FileValidatorInterface;

class FileValidator implements FileValidatorInterface
{
    public function resourceExists(string $path): bool
    {
        return file_exists($path);
    }

    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    public function isDir(string $path): bool
    {
        return is_dir($path);
    }

    public function isReadable(string $path): bool
    {
        return is_readable($path);
    }

    public function isReadableDir(string $path): bool
    {
        return $this->isDir($path) && $this->isReadable($path);
    }


    public function isReadableFile(string $path): bool
    {
        return $this->isFile($path) && $this->isReadable($path);
    }


    public function isValidExtension(string $file, array $extensions): bool
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        return in_array($extension, $extensions, true);
    }
}