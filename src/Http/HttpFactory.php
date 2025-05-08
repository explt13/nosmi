<?php

namespace Explt13\Nosmi\Http;

use Explt13\Nosmi\Interfaces\ExchangeInterface;
use Explt13\Nosmi\Interfaces\HttpFactoryInterface;
use Explt13\Nosmi\Interfaces\LightClientInterface;
use Explt13\Nosmi\Interfaces\LightRequestInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Interfaces\ReadExchangeInterface;
use Explt13\Nosmi\Interfaces\WriteExchangeInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

class HttpFactory implements HttpFactoryInterface
{
    protected $factory;
    public function __construct()
    {
        $this->factory = new Psr17Factory();
    }

    public function createRequest(string $method, $uri): LightRequestInterface
    {
        $request = $this->factory->createRequest($method, $uri);
        return new Request($request, $this);
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): LightResponseInterface
    {
        $response = $this->factory->createResponse($code, $reasonPhrase);
        return new Response($response, $this);
    }

    public function createServerRequest(?string $method = null, $uri = null, array $serverParams = []): LightServerRequestInterface
    {
        if (is_null($uri)) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $hostname = $_SERVER['SERVER_NAME'];
            $port = $_SERVER['SERVER_PORT'];
            $url = $_SERVER['REQUEST_URI'];
        }

        $method ??= $_SERVER['REQUEST_METHOD'];
        $method = strtoupper($method);
        $uri ??= "$scheme://$hostname:$port$url";
        $server_request = $this->factory->createServerRequest($method, $uri, $serverParams);
        return new ServerRequest($server_request, $this);
    }

    public function createStream(string $content = ''): StreamInterface
    {
        return $this->factory->createStream($content);
    }
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return $this->factory->createStreamFromFile($filename, $mode);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return $this->factory->createStreamFromResource($resource);
    }

    public function createUploadedFile(StreamInterface $stream, ?int $size = null, int $error = \UPLOAD_ERR_OK, ?string $clientFilename = null, ?string $clientMediaType = null): UploadedFileInterface
    {
        return $this->factory->createUploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
    }

    public function createUri(string $uri = ''): UriInterface
    {
        return $this->factory->createUri($uri);
    }

    public function createClient(): LightClientInterface
    {
        return new Client($this);
    }
}