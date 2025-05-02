<?php

namespace Explt13\Nosmi\Interfaces;

use Explt13\Nosmi\Http\Response;

interface LightClientInterface
{
    /**
     * Sends a GET request to the specified URL.
     *
     * @param string $url The URL to send the GET request to.
     * @param array $headers Optional headers to include in the request.
     * @return Response The response from the server.
     */
    public function get(string $url, array $headers = []): LightResponseInterface;
     
    /**
     * Sends a POST request to the specified URL.
     *
     * @param string $url The URL to send the POST request to.
     * @param array $headers Optional headers to include in the request.
     * @param string $body Optional body content for the POST request.
     * @return Response The response from the server.
     */
    public function post(string $url, array $headers = [], string $body = ''): LightResponseInterface;
     
    /**
     * Sends a PUT request to the specified URL.
     *
     * @param string $url The URL to send the PUT request to.
     * @param array $headers Optional headers to include in the request.
     * @param string $body Optional body content for the PUT request.
     * @return Response The response from the server.
     */
    public function put(string $url, array $headers = [], string $body = ''): LightResponseInterface;
    
    /**
     * Sends a DELETE request to the specified URL.
     *
     * @param string $url The URL to send the DELETE request to.
     * @param array $headers Optional headers to include in the request.
     * @return Response The response from the server.
     */
    public function delete(string $url, array $headers = []): LightResponseInterface;
}