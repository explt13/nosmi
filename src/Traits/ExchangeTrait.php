<?php

namespace Explt13\Nosmi\Traits;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

trait ExchangeTrait
{
    public function getHeader(string $name): array
    {
        return $this->exchange->getHeader($name);
    }

    public function withHeader(string $name, $value): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withHeader($name, $value);
        return $clone;
    }

    public function withAddedHeader(string $name, $value): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withAddedHeader($name, $value);
        return $clone;
    }

    public function withoutHeader(string $name): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withoutHeader($name);
        return $clone;
    }
    
    public function hasHeader(string $name): bool
    {
        return $this->exchange->hasHeader($name);
    }

    public function getHeaders(): array
    {
        return $this->exchange->getHeaders();
    }

    public function getHeaderLine(string $name): string
    {
        return $this->exchange->getHeaderLine($name);
    }

    public function getBody(): StreamInterface
    {
        return $this->exchange->getBody();
    }

    public function withBody(StreamInterface $body): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withBody($body);
        return $clone;
    }

    public function getProtocolVersion(): string
    {
        return $this->exchange->getProtocolVersion();
    }

    public function withProtocolVersion(string $version): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withProtocolVersion($version);
        return $clone;
    }
}