<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Cache\CacheFactory;
use Explt13\Nosmi\Interfaces\CacheFactoryInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;

class RateLimiterMiddleware extends Middleware
{
    private int $limit;
    private int $ttl; // time to live in seconds
    private CacheFactoryInterface $cache_factory;

    public function __construct(int $limit = 15, int $ttl = 60)
    {
        $this->cache_factory = new CacheFactory(AppConfig::getInstance());
        $this->limit = $limit;
        $this->ttl = $ttl;
    }

    protected function processRequest(LightServerRequestInterface $request): ?LightServerRequestInterface
    {
        $cache = $this->cache_factory->createCacheBasedOnConfigHandler();
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';
        $key = sha1("rate-limit:{$ip}");

        // Initialize or cleanup old records
        if (!$cache->has($key)) {
            $cache->set($key, 0, $this->ttl);
        }
        $tried_times = $cache->get($key);
        ++$tried_times;
        $cache->update($key, $tried_times);

        if ($tried_times > $this->limit) {
            $response = $this->createEarlyResponse(429, 'Rate limit exceeded. Try again later.');
            $response = $response->withHeaders(
                [
                    'Content-Type' => 'application/json',
                    'Retry-After' => (string) $this->ttl,
                    'X-RateLimit-Limit' => (string) $this->limit,
                    'X-RateLimit-Remaining' => (string) 0,
                    'X-RateLimit-Reset' => (string) $cache->getTtl($key),
                ],
            );
            $this->earlyResponse($response);
            return null;
        }
        return $request;
    }
    
    protected function processResponse(LightResponseInterface $response, LightServerRequestInterface $request): LightResponseInterface
    {
        $cache = $this->cache_factory->createCacheBasedOnConfigHandler();
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';
        $key = sha1("rate-limit:{$ip}");
        return $response
                ->withHeader('X-RateLimit-Limit', (string)$this->limit)
                ->withHeader('X-RateLimit-Remaining', (string)($this->limit - $cache->get($key)))
                ->withHeader('X-RateLimit-Reset', (string)$cache->getTtl($key));
    }
}