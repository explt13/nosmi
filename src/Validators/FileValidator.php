<?php
namespace Explt13\Nosmi\Validators;

use Explt13\Nosmi\Exceptions\InvalidResourceException;

class FileValidator
{
    /**
     * Checks for the existence of the file or the directory
     * 
     * @param string $path The path to the resource.
     * @return bool Returns true if the file or directory exists, false otherwise.
     *
     */
    public static function resourceExists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Checks if the given path is a file.
     *
     * @param string $path The path to the resource.
     * @return bool Returns true if the path is a file, false otherwise.
     */
    public static function isFile(string $path): bool
    {
        return is_file($path);
    }

    /**
     * Checks if the given path is a directory.
     *
     * @param string $path The path to the resource.
     * @return bool Returns true if the path is a directory, false otherwise.
     */
    public static function isDir(string $path): bool
    {
        return is_dir($path);
    }

    /**
     * Checks if the file or directory of the given path is readable.
     *
     * @param string $path The path to the resource.
     * @return bool Returns true if the file is readable, false otherwise.
     */
    public static function isReadable(string $path): bool
    {
        return is_readable($path);
    }

    /**
     * Checks if the file of the given path is readable and a directory.
     *
     * @param string $path The path to the resource.
     * @return bool Returns true if the file is readable, false otherwise.
     */
    public static function isReadableDir(string $path): bool
    {
        return self::isDir($path) && self::isReadable($path);
    }

    /**
     * Checks if the file of the given path is readable and a file.
     *
     * @param string $path The path to the resource.
     * @return bool Returns true if the file is readable, false otherwise.
     */
    public static function isReadableFile(string $path): bool
    {
        return self::isFile($path) && self::isReadable($path);
    }

    /**
     * Checks if the file of the given path is readable and a directory.
     *
     * @param string $path The path to the resource.
     * @return void
     * @throws InvalidResourceException
     */
    public static function validateDirIsReadable(string $path): void
    {
        if (!self::isReadableDir($path)) {
            throw InvalidResourceException::withMessage('The specified folder is not a valid directory: ' . $path);
        }
    }

     /**
     * Checks if the file of the given path is readable and a file.
     *
     * @param string $path The path to the resource.
     * @return void
     * @throws InvalidResourceException
     */
    public static function validateFileIsReadable(string $path): void
    {
        if (!self::isReadableFile($path)) {
            throw InvalidResourceException::withMessage('The specified path is not a valid file: ' . $path);
        }
    }

     /**
     * Checks if the file of the given path is readable resource.
     *
     * @param string $path The path to the resource.
     * @return void
     * @throws InvalidResourceException
     */
    public static function validateResourceIsReadable(string $path): void
    {
        if (!self::resourceExists($path) || !self::isReadable($path)) {
            throw InvalidResourceException::withMessage('The specified folder is not a valid directory: ' . $path);
        }
    }


    /**
     * Validates if the given file's extension is in the list of allowed extensions.
     *
     * @param string $extension The file to validate.
     * @param array $extensions The list of allowed extensions.
     * @return bool Returns true if the extension is valid, false otherwise.
     */
    public static function isValidExtension(string $file, array $extensions): bool
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        return in_array($extension, $extensions, true);
    }
}