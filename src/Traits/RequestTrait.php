<?php

namespace Explt13\Nosmi\Traits;

use Psr\Http\Message\UriInterface;

trait RequestTrait
{
    
    public function getUri(): UriInterface
    {
        return $this->exchange->getUri();
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withUri($uri, $preserveHost);
        return $clone;
    }

    public function getMethod(): string
    {
        return $this->exchange->getMethod();
    }

    public function withMethod(string $method): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withMethod($method);
        return $clone;
    }

    public function getRequestTarget(): string
    {
        return $this->exchange->getRequestTarget();
    }

    public function withRequestTarget(string $requestTarget): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withRequestTarget($requestTarget);
        return $clone;
    }
}