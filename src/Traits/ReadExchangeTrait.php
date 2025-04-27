<?php

namespace Explt13\Nosmi\Traits;

trait ReadExchangeTrait
{
    public function readBody(int $length): string
    {
        $body = $this->getBody();
        if ($body->eof()) {
            $body->rewind(); // Optionally rewind the stream
        }
        return $body->read($length);
    }

    public function getBodyContent(): string
    {
        $body = $this->getBody();
        $body->rewind();
        return (string) $body;
    }

    public function getParsedBody(): array
    {
        $contentType = $this->getContentType();
        $body = $this->getBody();
        $body->rewind(); // Ensure the stream is at the beginning
        $bodyContent = $body->getContents();


        if (str_contains($contentType, 'application/json')) {
            $parsed = json_decode($bodyContent, true, 512, JSON_THROW_ON_ERROR);
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

    public function getJsonBody(bool $asArray = true)
    {
        $body = (string) $this->getBody();
        return json_decode($body, $asArray, 512, JSON_THROW_ON_ERROR);
    }

    public function getContentType(): ?string
    {
        return $this->getHeaderLine('Content-Type') ?: null;
    }

    public function getUploadedFiles(): array
    {
        return $_FILES ?? [];
    }

    public function isJson(): bool
    {
        $contentType = $this->getHeaderLine('Content-Type');
        return stripos($contentType, 'application/json') !== false;
    }
}