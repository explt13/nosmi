<?php

namespace Explt13\Nosmi\Interfaces;

interface FileValidatorInterface
{
    /**
     * Checks for the existence of the file or the directory
     * 
     * @param string $path The path to the resource.
     * @return bool Returns true if the file or directory exists, false otherwise.
     *
     */
    public function resourceExists(string $path): bool;

    /**
     * Checks if the given path is a file.
     *
     * @param string $path The path to the resource.
     * @return bool Returns true if the path is a file, false otherwise.
     */
    public function isFile(string $path): bool;

    
    /**
     * Checks if the given path is a directory.
     *
     * @param string $path The path to the resource.
     * @return bool Returns true if the path is a directory, false otherwise.
     */
    public function isDir(string $path): bool;

    /**
     * Checks if the file or directory of the given path is readable.
     *
     * @param string $path The path to the resource.
     * @return bool Returns true if the file is readable, false otherwise.
     */
    public function isReadable(string $path): bool;

    /**
     * Checks if the file of the given path is readable and a directory.
     *
     * @param string $path The path to the resource.
     * @return bool Returns true if the file is readable, false otherwise.
     */
    public function isReadableDir(string $path): bool;

    /**
     * Checks if the file of the given path is readable and a file.
     *
     * @param string $path The path to the resource.
     * @return bool Returns true if the file is readable, false otherwise.
     */
    public function isReadableFile(string $path): bool;

    /**
     * Validates if the given file's extension is in the list of allowed extensions.
     *
     * @param string $extension The file to validate.
     * @param array $extensions The list of allowed extensions.
     * @return bool Returns true if the extension is valid, false otherwise.
     */
    public function isValidExtension(string $file, array $extensions): bool;
}
