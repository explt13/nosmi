<?php

namespace Explt13\Nosmi\Interfaces;

interface FileValidatorInterface
{
    /**
     * Checks for the existence of the file or the directory
     * 
     * @param string $path The path to the file or directory to check.
     * @return bool Returns true if the file or directory exists, false otherwise.
     *
     */
    public function fileExists(string $path): bool;

    /**
     * Checks if the given path is a file.
     *
     * @param string $path The path to check.
     * @return bool Returns true if the path is a file, false otherwise.
     */
    public function isFile(string $path): bool;

    /**
     * Checks if the file at the given path is readable.
     *
     * @param string $path The path to the file to check.
     * @return bool Returns true if the file is readable, false otherwise.
     */
    public function isReadable(string $path): bool;

    /**
     * Validates if the given file extension is in the list of allowed extensions.
     *
     * @param string $extension The file extension to validate.
     * @param array $extensions The list of allowed extensions.
     * @return bool Returns true if the extension is valid, false otherwise.
     */
    public function isValidExtension(string $extension, array $extensions): bool;
}
