<?php
namespace Explt13\Nosmi\Routing;

use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

class Response implements LightResponseInterface
{
    private ResponseInterface $response;
    private Psr17Factory $factory;

    public function __construct(int $status = 200)
    {
        $this->factory = new Psr17Factory();
        $this->response = $this->factory->createResponse($status);
    }

    public function withHeader(string $name, string $value): self
    {
        $this->response = $this->response->withHeader($name, $value);
        return $this;
    }

    public function withStatus(int $code): self
    {
        $this->response = $this->response->withStatus($code);
        return $this;
    }

    public function withCookie(string $name, string $value, array $options = []): self
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
        $this->withHeader('Set-Cookie', $cookie);
        return $this;
    }

    public function withCors(string $origin = '*'): self
    {
        return $this
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    }

    public function json(array $data): self
    {
        $this->withHeader('Content-Type', 'application/json');
        $body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $this->response->getBody()->write($body);
        return $this;
    }

    public function html(string $html): self
    {
        $this->withHeader('Content-Type', 'text/html; charset=utf-8');
        $this->response->getBody()->write($html);
        return $this;
    }

    public function text(string $text): self
    {
        $this->withHeader('Content-Type', 'text/plain; charset=utf-8');
        $this->response->getBody()->write($text);
        return $this;
    }

    public function download(string $filePath, ?string $fileName = null): self
    {
        if (!file_exists($filePath)) {
            return $this->error(404, "File not found");
        }

        $fileName = $fileName ?? basename($filePath);
        $this->withHeader('Content-Type', mime_content_type($filePath))
            ->withHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->withHeader('Content-Length', (string) filesize($filePath));

        $this->response->getBody()->write(file_get_contents($filePath));
        return $this;
    }

    public function redirect(string $url, int $status = 302): self
    {
        $this->response = $this->response
            ->withHeader('Location', $url)
            ->withStatus($status);
        return $this;
    }

    public function xml(string $xml): self
    {
        $this->withHeader('Content-Type', 'application/xml; charset=utf-8');
        $this->response->getBody()->write($xml);
        return $this;
    }

    public function error(int $code = 400, ?string $message = null, array $additionalData = []): self
    {
        $this->withStatus($code);
        $data = array_merge([
            'error' => true,
            'message' => $message ?? $this->response->getReasonPhrase() ?? 'Unknown error'
        ], $additionalData);

        return $this->json($data);
    }

    public function stream(callable $streamCallback): self
    {
        $this->withHeader('Content-Type', 'application/octet-stream');
        ob_start();
        $streamCallback();
        $this->response->getBody()->write(ob_get_clean());
        return $this;
    }

    public function streamFile(string $filePath, ?string $fileName = null): self
    {
        if (!file_exists($filePath)) {
            return $this->error(404, "File not found");
        }

        $fileName = $fileName ?? basename($filePath);
        $this->withHeader('Content-Type', mime_content_type($filePath))
            ->withHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->withHeader('Content-Length', (string) filesize($filePath));

        $stream = fopen($filePath, 'rb');
        if ($stream) {
            while (!feof($stream)) {
                echo fread($stream, 8192);
                flush();
            }
            fclose($stream);
        }
        return $this;
    }

    public function empty(int $status = 204): self
    {
        return $this->withStatus($status);
    }

    public function send(array $additionalHeaders = []): void
    {
        http_response_code($this->response->getStatusCode());

        foreach ($this->response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        foreach ($additionalHeaders as $name => $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }

        echo $this->response->getBody();
    }

    public function get(): ResponseInterface
    {
        return $this->response;
    }
}