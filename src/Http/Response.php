<?php
namespace Explt13\Nosmi\Http;

use Explt13\Nosmi\Interfaces\ExchangeInterface;
use Explt13\Nosmi\Interfaces\HttpFactoryInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\ReadExchangeInterface;
use Explt13\Nosmi\Interfaces\WriteExchangeInterface;
use Explt13\Nosmi\Traits\ExchangeTrait;
use Explt13\Nosmi\Traits\ReadExchangeTrait;
use Explt13\Nosmi\Traits\WriteExchangeTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Response implements LightResponseInterface, ReadExchangeInterface, WriteExchangeInterface, ExchangeInterface
{
    use ExchangeTrait;
    use ReadExchangeTrait;
    use WriteExchangeTrait;

    private ResponseInterface $exchange;
    private HttpFactoryInterface $factory;

    public function __construct(ResponseInterface $psr_response, HttpFactoryInterface $factory)
    {
        $this->exchange = $psr_response;
        $this->factory = $factory;
    }

    public function getStatusCode(): int
    {
        return $this->exchange->getStatusCode();
    }

    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        $clone = clone $this;
        $clone->exchange = $this->exchange->withStatus($code, $reasonPhrase);
        return $clone;
    }

    public function getReasonPhrase(): string
    {
        return $this->exchange->getReasonPhrase();
    }

    public function withJson(array $data): static
    {
        $body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        $stream = $this->factory->createStream($body);
        return $this->withHeader('Content-Type', 'application/json')->withBody($stream);
    }

    public function withXml(string $xml): static
    {
        $stream = $this->factory->createStream($xml);
        return $this->withHeader('Content-Type', 'application/xml; charset=utf-8')->withBody($stream);
    }

    public function withHtml(string $html): static
    {
        $stream = $this->factory->createStream($html);
        return $this->withHeader('Content-Type', 'text/html; charset=utf-8')->withBody($stream);
    }

    public function withText(string $text): static
    {
        $stream = $this->factory->createStream($text);
        return $this->withHeader('Content-Type', 'text/plain; charset=utf-8')->withBody($stream);
    }

    public function withCookieHeader(string $name, string $value, array $options = []): static
    {
        $cookie = sprintf('%s=%s', $name, urlencode($value));
        if (isset($options['expires'])) {
            $cookie .= '; Expires=' . gmdate('D, d-M-Y H:i:s T', $options['expires']);
        }
        if (isset($options['path'])) {
            $cookie .= '; Path=' . $options['path'];
        }
        if (isset($options['domain'])) {
            $cookie .= '; Domain=' . $options['domain'];
        }
        if (!empty($options['secure'])) {
            $cookie .= '; Secure';
        }
        if (!empty($options['httponly'])) {
            $cookie .= '; HttpOnly';
        }
        
        return $this->withHeader('Set-Cookie', $cookie);
    }

    public function withCorsHeader(string $origin = '*'): static
    {
        return $this
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Max-Age', '86400');
    }

    public function withDownload(string $filePath, ?string $fileName = null): static
    {
        if (!file_exists($filePath)) {
            return $this->withError(404, "File not found");
        }
        $fileName = $fileName ?? basename($filePath);
    
        $stream = $this->factory->createStream(file_get_contents($fileName));
        return $this->withHeader('Content-Type', mime_content_type($filePath))
                    ->withHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                    ->withHeader('Content-Length', (string) filesize($filePath))
                    ->withBody($stream);
    }

    public function withRedirect(string $url, int $status = 302): static
    {
        return $this->withHeader('Location', $url)
                    ->withStatus($status);
    }


    public function withError(int $code = 400, ?string $message = null, array $additionalData = []): static
    {
        $data = array_merge([
            'error' => true,
            'message' => $message ?? $this->exchange->getReasonPhrase() ?? 'Unknown error'
        ], $additionalData);

        return $this->withStatus($code)->withJson($data);
    }

    public function withStream(callable $streamCallback): static
    {
        ob_start();
        $streamCallback();
        $data = ob_get_clean();
        $stream = $this->factory->createStream($data);
        return $this->withHeader('Content-Type', 'application/octet-stream')->withBody($stream);
    }

    public function withStreamFile(string $filePath, ?string $fileName = null): static
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return $this->withError(404, "File not found or not readable");
        }

        $fileName = $fileName ?? basename($filePath);
        $stream = $this->factory->createStreamFromFile($filePath, 'rb');

        return $this->withHeader('Content-Type', mime_content_type($filePath))
                    ->withHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                    ->withHeader('Content-Length', (string) filesize($filePath))
                    ->withBody($stream);
    }

    public function withEmpty(int $status = 204): static
    {
        return $this->withStatus($status);
    }

    public function send(array $additionalHeaders = []): void
    {
        http_response_code($this->exchange->getStatusCode());

        foreach ($this->exchange->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        foreach ($additionalHeaders as $name => $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }

        echo $this->exchange->getBody();
    }
}