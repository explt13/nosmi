<?php

namespace Explt13\Nosmi\Http;

use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Interfaces\ReadExchangeInterface;
use Explt13\Nosmi\Traits\ExchangeTrait;
use Explt13\Nosmi\Traits\ReadExchangeTrait;
use Explt13\Nosmi\Traits\RequestTrait;
use Nyholm\Psr7\ServerRequest as Psr7ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequest implements LightServerRequestInterface, ReadExchangeInterface
{
    use ExchangeTrait;
    use RequestTrait;
    use ReadExchangeTrait;

    private ServerRequestInterface $exchange;

    public function __construct(
        ServerRequestInterface $psr_server_request, 
    )
    {
        $this->exchange = $psr_server_request;
        foreach ($this->getServerHeaders() as $headername => $value) {
            $this->exchange = $this->exchange->withHeader($headername, $value);
        }
    }
    public static function capture(): static
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $hostname = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'];
        $url = $_SERVER['REQUEST_URI'];
        return new static(new Psr7ServerRequest($method, "$scheme://$hostname:$port$url", [], null, "1.1", $_SERVER));
    }

    public function getServerParams(): array
    {
        return $this->exchange->getServerParams();
    }

    public function getCookieParams(): array
    {
        return $this->exchange->getCookieParams();
    }

    public function withCookieParams(array $cookies): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withCookieParams($cookies);
        return $clone;
    }

    public function withQueryParams(array $query): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withQueryParams($query);
        return $clone;
    }

    public function getQueryParams(): array
    {
        parse_str($this->getUri()->getQuery(), $queryParams);
        return $queryParams;
    }

    public function getQueryParam(string $name, $default = null): string|array|null
    {
        parse_str($this->getUri()->getQuery(), $queryParams);
        return $queryParams[$name] ?? $default;
    }

    public function withUploadedFiles(array $uploadedFiles): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withUploadedFiles($uploadedFiles);
        return $clone;
    }

    public function withParsedBody($data): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withParsedBody($data);
        return $clone;
    }

    public function getAttributes(): array
    {
        return $this->exchange->getAttributes();
    }

    public function getAttribute(string $name, $default = null): mixed
    {
        return $this->exchange->getAttribute($name, $default);
    }

    public function withAttribute(string $name, $value): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withAttribute($name, $value);
        return $clone;
    }

    public function withoutAttribute(string $name): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withoutAttribute($name);
        return $clone;
    }

    public function isHttps(): bool
    {
        return $this->getUri()->getScheme() === 'https';
    }

    public function getProtocol(): string
    {
        return $this->exchange->getProtocolVersion();
    }

    public function getMethod(): string
    {
        return $this->exchange->getMethod();
    }

    public function isGet(): bool
    {
        return $this->exchange->getMethod() === 'GET';
    }

    public function isPost(): bool
    {
        return $this->exchange->getMethod() === 'POST';
    }

    public function isPut(): bool
    {
        return $this->exchange->getMethod() === 'PUT';
    }

    public function isPatch(): bool
    {
        return $this->exchange->getMethod() === 'PATCH';
    }

    public function isDelete(): bool
    {
        return $this->exchange->getMethod() === 'DELETE';
    }

    public function isOptions(): bool
    {
        return $this->exchange->getMethod() === 'OPTIONS';
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

    private function getServerHeaders(): array
    {
        $headers = [];
        foreach ($this->getServerParams() as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace('_', '-', substr($key, 5));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }
}