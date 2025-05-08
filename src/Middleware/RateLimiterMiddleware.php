<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;

class RateLimitingMiddleware extends Middleware
{
    private int $limit;
    private int $ttl; // time to live in seconds

    // Simulated in-memory storage (use Redis or persistent storage in production)
    private static array $requests = [];

    public function __construct(int $limit = 60, int $ttl = 60)
    {
        $this->limit = $limit;
        $this->ttl = $ttl;
    }

    protected function processRequest(LightServerRequestInterface $request): ?LightServerRequestInterface
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';

        $now = time();
        $key = sha1("rate-limit:{$ip}");

        // Initialize or cleanup old records
        if (!isset(self::$requests[$key])) {
            self::$requests[$key] = ['count' => 0, 'reset_at' => $now + $this->ttl];
        } elseif (self::$requests[$key]['reset_at'] < $now) {
            self::$requests[$key] = ['count' => 0, 'reset_at' => $now + $this->ttl];
        }

        self::$requests[$key]['count']++;

        if (self::$requests[$key]['count'] > $this->limit) {
            $response = $this->createEarlyResponse(429, 'Rate limit exceeded. Try again later.');
            $response = $response->withHeaders(
                [
                    'Content-Type' => 'application/json',
                    'Retry-After' => $this->ttl,
                    'X-RateLimit-Limit' => $this->limit,
                    'X-RateLimit-Remaining' => 0,
                    'X-RateLimit-Reset' => self::$requests[$key]['reset_at'],
                ],
            );
            $this->earlyResponse($response);
            return null;
        }
        return $request;
    }
    
    protected function processResponse(LightResponseInterface $response): LightResponseInterface
    {
        return $response
                ->withHeader('X-RateLimit-Limit', (string)$this->limit)
                ->withHeader('X-RateLimit-Remaining', (string)($this->limit - self::$requests[$key]['count']))
                ->withHeader('X-RateLimit-Reset', (string)self::$requests[$key]['reset_at']);
    }
}