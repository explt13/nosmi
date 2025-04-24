<?php

namespace Explt13\Nosmi\Interfaces;

interface IncomingRequestInterface
{
    /**
     * Get the entire body content as a string.
     *
     * @return string The body content.
     */
    public function getBodyContent(): string;

     /**
     * Read a specific number of bytes from the body.
     *
     * @param int $length The number of bytes to read.
     * @return string The read content.
     */
    public function readBody(int $length): string;

    /**
     * Get the uploaded files as an array.
     *
     * @return array An array of uploaded files.
     */
    public function getUploadedFiles(): array;

    /**
     * Determines if the request body is in JSON format.
     *
     * @return bool True if the request body is JSON, false otherwise.
     */
    public function isJson(): bool;

    /**
     * Retrieves the parsed body of the request.
     *
     * @return array The parsed body as an associative array.
     */
    public function getParsedBody(): array;

    /**
     * Gets the content type of the request.
     *
     * @return string|null The content type of the request, or null if not set.
     */
    public function getContentType(): ?string;

    /**
     * Retrieves the JSON-decoded body of the request.
     *
     * @param bool $asArray Whether to return the JSON as an associative array (true) 
     *                      or as an object (false). Defaults to true.
     * @return mixed The JSON-decoded body, as an array or object.
     */
    public function getJsonBody(bool $asArray = true);
}