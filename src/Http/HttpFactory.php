<?php

namespace Explt13\Nosmi\Http;

use Explt13\Nosmi\Interfaces\IncomingRequestInterface;
use Explt13\Nosmi\Interfaces\LightRequestInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Interfaces\OutgoingRequestInterface;
use Explt13\Nosmi\Interfaces\Psr17FactoryInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class HttpFactory implements Psr17FactoryInterface
{
    private $factory;
    public function __construct(
        RequestFactoryInterface&
        ResponseFactoryInterface&
        UriFactoryInterface&
        StreamFactoryInterface&
        UploadedFileFactoryInterface&
        ServerRequestFactoryInterface $factory
    )
    {
        $this->factory = $factory;
    }

    public function createRequest(string $method, $uri): LightRequestInterface&OutgoingRequestInterface&IncomingRequestInterface
    {
        $request = $this->factory->createRequest($method, $uri);
        return new Request($request, $this->factory);
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): LightResponseInterface
    {
        $response = $this->factory->createResponse($code, $reasonPhrase);
        return new Response($response, $this->factory);
    }

    public function createServerRequest(?string $method = null, $uri = null, array $serverParams = []): LightServerRequestInterface&IncomingRequestInterface
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $hostname = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'];
        $url = $_SERVER['REQUEST_URI'];
        
        $method ??= $_SERVER['REQUEST_METHOD'];
        $uri ??= "$scheme://$hostname:$port$url";
        $server_request = $this->factory->createServerRequest($method, $uri, $serverParams);
        return new ServerRequest($server_request, $this->factory);
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
}