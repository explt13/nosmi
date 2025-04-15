<?php

namespace Explt13\Nosmi\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface LightResponseInterface
{
    public function withHeader(string $name, string $value): self;

    public function withStatus(int $code): self;

    public function withCookie(string $name, string $value, array $options = []): self;

    public function withCors(string $origin = '*'): self;

    public function json(array $data): self;

    public function html(string $html): self;

    public function text(string $text): self;

    public function download(string $filePath, ?string $fileName = null): self;

    public function redirect(string $url, int $status = 302): self;

    public function xml(string $xml): self;

    public function error(int $code = 400, ?string $message = null, array $additionalData = []): self;

    public function stream(callable $streamCallback): self;

    public function streamFile(string $filePath, ?string $fileName = null): self;

    public function empty(int $status = 204): self;

    public function send(array $additionalHeaders = []): void;

    public function get(): ResponseInterface;
}