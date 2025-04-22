<?php

namespace Explt13\Nosmi\Routing;

use Explt13\Nosmi\Exceptions\NotInArrayException;
use Explt13\Nosmi\Interfaces\LightRequestInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class Request implements LightRequestInterface
{
    private const AVAILABLE_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
    private RequestInterface $request;

    public function __construct(string $method, string $uri)
    {
        $this->validateIsAllowedMethod($method);
        $factory = new Psr17Factory();
        $this->request = $factory->createRequest($method, $uri);
        foreach ($this->getHeadersFromServer() as $name => $value) {
            $this->request = $this->request->withHeader($name, $value);
        }
    }

    public static function init()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $hostname = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'];
        $url = $_SERVER['REQUEST_URI'];
        return new Request($method, "$scheme://$hostname:$port$url");
    }

    public function getHeader(string $name): array
    {
        return $this->request->getHeader($name);
    }

    public function getHeaders(): array
    {
        return $this->request->getHeaders();
    }

    public function getHeaderLine(string $name): string
    {
        return $this->request->getHeaderLine($name);
    }

    public function getBodyContent(): string
    {
        $body = $this->request->getBody();
        $body->rewind();
        return $body->getContents();
    }

    public function readBody(int $length): string
    {
        $body = $this->request->getBody();
        if ($body->eof()) {
            $body->rewind(); // Optionally rewind the stream
        }
        return $body->read($length);
    }

    public function getParsedBody(): array
    {
        $contentType = $this->getContentType();
        $body = $this->request->getBody();
        $body->rewind(); // Ensure the stream is at the beginning
        $bodyContent = $body->getContents();


        if (str_contains($contentType, 'application/json')) {
            $parsed = json_decode($bodyContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON body: ' . json_last_error_msg());
            }
            return $parsed ?? [];
        }

        if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            parse_str($bodyContent, $parsedBody);
            return $parsedBody;
        }
        return [];
    }

    public function getUploadedFiles(): array
    {
        return $_FILES ?? [];
    }

    public function getDetailed(): RequestInterface
    {
        return $this->request;
    }

    public function getUri(): UriInterface
    {
        return $this->request->getUri();
    }

    public function isHttps(): bool
    {
        return $this->getUri()->getScheme() === 'https';
    }

    public function getProtocol(): string
    {
        return $this->request->getProtocolVersion();
    }

    public function getMethod(): string
    {
        return $this->request->getMethod();
    }

    public function isGet(): bool
    {
        return $this->request->getMethod() === 'GET';
    }

    public function isPost(): bool
    {
        return $this->request->getMethod() === 'POST';
    }

    public function isPut(): bool
    {
        return $this->request->getMethod() === 'PUT';
    }

    public function isPatch(): bool
    {
        return $this->request->getMethod() === 'PATCH';
    }

    public function isDelete(): bool
    {
        return $this->request->getMethod() === 'DELETE';
    }

    public function isOptions(): bool
    {
        return $this->request->getMethod() === 'OPTIONS';
    }

    public function isAjax(): bool
    {
        return strtolower($this->getHeaderLine('X-Requested-With')) === 'xmlhttprequest';
    }

    public function getClientIp(): ?string
    {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }

    public function getReferer(): string
    {
        return $this->getHeaderLine('Referer');
    }

    public function getUserAgent(): string
    {
        return $this->getHeaderLine('User-Agent');
    }

    public function getPath(): string
    {
        return $this->getUri()->getPath();
    }

    public function getSession(string $key, $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function getContentType(): string
    {
        return $this->getHeaderLine('Content-Type');
    }

    public function getQueryParam(string $name, $default = null): string|array|null
    {
        parse_str($this->getUri()->getQuery(), $queryParams);
        return $queryParams[$name] ?? $default;
    }

    public function getQueryParams(): array
    {
        parse_str($this->getUri()->getQuery(), $queryParams);
        return $queryParams;
    }

    public function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            if ($rule === 'required' && empty($data[$field])) {
                $errors[$field] = "$field is required.";
            }
            // more rules ... 
        }

        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }

        return $data;
    }

    private function validateIsAllowedMethod(string $method): bool
    {
        if (!in_array(strtoupper($method), self::AVAILABLE_METHODS)) {
            throw new NotInArrayException($method, self::AVAILABLE_METHODS);
        }
        return true;
    }

    private function getHeadersFromServer(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace('_', '-', substr($key, 5));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

}