<?php

namespace Explt13\Nosmi\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface LightResponseInterface extends ResponseInterface
{

    /**
     * Sends a JSON response.
     *
     * @param array $data The JSON data to send.
     * @return LightResponseInterface
     */
    public function withJson(array $data): LightResponseInterface;


    /**
     * Adds a cookie to the response.
     *
     * @param string $name    The name of the cookie.
     * @param string $value   The value of the cookie.
     * @param array  $options Additional options for the cookie (e.g., expiration, path, domain).
     * @return LightResponseInterface
     */
    public function withCookieHeader(string $name, string $value, array $options = []): LightResponseInterface;

    
    /**
     * Configures Cross-Origin Resource Sharing (CORS) headers.
     *
     * @param string $origin The allowed origin for CORS. Defaults to '*'.
     * @return LightResponseInterface
     */
    public function withCorsHeader(string $origin = '*'): LightResponseInterface;


    /**
     * Sends an HTML response.
     *
     * @param string $html The HTML content to send.
     * @return LightResponseInterface
     */
    public function withHtml(string $html): LightResponseInterface;

    /**
     * Sends a plain text response.
     *
     * @param string $text The text content to send.
     * @return LightResponseInterface
     */
    public function withText(string $text): LightResponseInterface;

    /**
     * Sends a file as a downloadable response.
     *
     * @param string      $filePath The path to the file.
     * @param string|null $fileName Optional custom name for the downloaded file.
     * @return LightResponseInterface
     */
    public function withDownload(string $filePath, ?string $fileName = null): LightResponseInterface;

    /**
     * Redirects the client to a specified URL.
     *
     * @param string $url    The URL to redirect to.
     * @param int    $status The HTTP status code for the redirect. Defaults to 302.
     * @return LightResponseInterface
     */
    public function withRedirectHeader(string $url, int $status = 302): LightResponseInterface;

    /**
     * Sends an XML response.
     *
     * @param string $xml The XML content to send.
     * @return LightResponseInterface
     */
    public function withXml(string $xml): LightResponseInterface;

    /**
     * Sends an error response with a specified status code and optional message.
     *
     * @param int         $code           The HTTP status code for the error. Defaults to 400.
     * @param string|null $message        An optional error message.
     * @param array       $additionalData Additional data to include in the error response.
     * @return LightResponseInterface
     */
    public function withError(int $code = 400, ?string $message = null, array $additionalData = []): LightResponseInterface;

    /**
     * Sends a streaming response using a callback.
     *
     * @param callable $streamCallback A callback function to handle the streaming logic.
     * @return LightResponseInterface
     */
    public function withStream(callable $streamCallback): LightResponseInterface;

    /**
     * Streams a file to the client.
     *
     * @param string      $filePath The path to the file.
     * @param string|null $fileName Optional custom name for the streamed file.
     * @return LightResponseInterface
     */
    public function withStreamFile(string $filePath, ?string $fileName = null): LightResponseInterface;

    /**
     * Sends an empty response with a specified status code.
     *
     * @param int $status The HTTP status code for the empty response. Defaults to 204.
     * @return LightResponseInterface
     */
    public function withEmpty(int $status = 204): LightResponseInterface;

    /**
     * Sends the response to the client with optional additional headers.
     *
     * @param array $additionalHeaders Additional headers to include in the response.
     * @return void
     */
    public function send(array $additionalHeaders = []): void;

}