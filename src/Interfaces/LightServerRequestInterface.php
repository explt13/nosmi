<?php

namespace Explt13\Nosmi\Interfaces;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

interface LightServerRequestInterface extends ServerRequestInterface
{
    /**
     * Check if the request is made over HTTPS.
     *
     * @return bool True if HTTPS, false otherwise.
     */
    public function isHttps(): bool;

    /**
     * Check if the request method is GET.
     *
     * @return bool True if GET, false otherwise.
     */
    public function isGet(): bool;

    /**
     * Check if the request method is POST.
     *
     * @return bool True if POST, false otherwise.
     */
    public function isPost(): bool;

    /**
     * Check if the request method is PUT.
     *
     * @return bool True if PUT, false otherwise.
     */
    public function isPut(): bool;

    /**
     * Check if the request method is PATCH.
     *
     * @return bool True if PATCH, false otherwise.
     */
    public function isPatch(): bool;

    /**
     * Check if the request method is DELETE.
     *
     * @return bool True if DELETE, false otherwise.
     */
    public function isDelete(): bool;

    /**
     * Check if the request method is OPTIONS.
     *
     * @return bool True if OPTIONS, false otherwise.
     */
    public function isOptions(): bool;

    /**
     * Check if the request is an AJAX request.
     *
     * @return bool True if AJAX, false otherwise.
     */
    public function isAjax(): bool;

    /**
     * Get the client's IP address.
     *
     * @return string|null The client IP address or null if not available.
     */
    public function getClientIp(): ?string;

    /**
     * Get the referer URL of the request.
     *
     * @return string The referer URL.
     */
    public function getReferer(): string;

    /**
     * Get the user agent string of the client.
     *
     * @return string The user agent string.
     */
    public function getUserAgent(): string;

    /**
     * Get the path of the request.
     *
     * @return string The request path.
     */
    public function getPath(): string;

    /**
     * Get a session value by key.
     *
     * @param string $key The session key.
     * @param mixed $default The default value if the key does not exist.
     * @return mixed The session value or the default value.
     */
    public function getSession(string $key, $default = null): mixed;

    /**
     * Validate the given data against the specified rules.
     *
     * @param array $data The data to validate.
     * @param array $rules The validation rules.
     * @return array The validated data.
     */
    public function validate(array $data, array $rules): array;
}