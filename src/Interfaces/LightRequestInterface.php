<?php

namespace Explt13\Nosmi\Interfaces;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

interface LightRequestInterface
{
    public function getHeader(string $name): array;
    
    public function getHeaders(): array;
    
    public function getHeaderLine(string $name): string;
    
    public function getBodyContent(): string;
    
    public function readBody(int $length): string;
    
    public function getParsedBody(): array;
    
    public function getUploadedFiles(): array;
    
    public function getDetailed(): RequestInterface;
    
    public function getUri(): UriInterface;
    
    public function isHttps(): bool;
    
    public function getProtocol(): string;
    
    public function getMethod(): string;
    
    public function isGet(): bool;
    
    public function isPost(): bool;
    
    public function isPut(): bool;
    
    public function isPatch(): bool;
    
    public function isDelete(): bool;
    
    public function isOptions(): bool;
    
    public function isAjax(): bool;
    
    public function getClientIp(): ?string;
    
    public function getReferer(): string;
    
    public function getUserAgent(): string;
    
    public function getPath(): string;
    
    public function getSession(string $key, $default = null): mixed;
    
    public function getContentType(): string;
    
    public function getQueryParam(string $name, $default = null): string|array|null;
    
    public function getQueryParams(): array;
    
    public function validate(array $data, array $rules): array;
}