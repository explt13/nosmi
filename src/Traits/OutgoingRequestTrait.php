<?php

namespace Explt13\Nosmi\Traits;

trait OutgoingRequestTrait
{
    public function withJson(array $data): static
    {
        $body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $stream = $this->factory->createStream($body);
        return $this->withHeader('Content-Type', 'application/json')->withBody($stream);
    }

    public function withXml(string $xml): static
    {
        $stream = $this->factory->createStream($xml);
        return $this->withHeader('Content-Type', 'application/xml; charset=utf-8')->withBody($stream);
    }

    public function withFormBody(array $data): static
    {
        $formBody = http_build_query($data);
        $stream = $this->factory->createStream($formBody);
        return  $this->exchange->withBody($stream)
                              ->withHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    public function withMultipartBody(array $fields, array $files): static
    {
        $boundary = uniqid();
        $body = '';

        // Add fields
        foreach ($fields as $name => $value) {
            $body .= "--$boundary\r\n";
            $body .= "Content-Disposition: form-data; name=\"$name\"\r\n\r\n";
            $body .= "$value\r\n";
        }

        // Add files
        foreach ($files as $name => $file) {
            $body .= "--$boundary\r\n";
            $body .= "Content-Disposition: form-data; name=\"$name\"; filename=\"{$file['filename']}\"\r\n";
            $body .= "Content-Type: {$file['mime']}\r\n\r\n";
            $body .= "{$file['content']}\r\n";
        }

        $body .= "--$boundary--\r\n";

        $clone = clone $this;
        $stream = $this->factory->createStream($body);
        $clone->exchange = $this->exchange->withBody($stream)
                                        ->withHeader('Content-Type', "multipart/form-data; boundary=$boundary");
        return $clone;
    }

    public function withBearerToken(string $token): static
    {
        return $this->withHeader('Authorization', 'Bearer ' . $token);
    }

    public function withQueryParams(array $params): static
    {
        $uri = $this->getUri();
        $existingParams = [];
        parse_str($uri->getQuery(), $existingParams);
        $newParams = array_merge($existingParams, $params);
        $newQuery = http_build_query($newParams);
    
        $clone = clone $this;
        $clone->exchange = $this->exchange->withUri($uri->withQuery($newQuery));
        return $clone;
    }
}