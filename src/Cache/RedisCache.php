<?php
namespace Explt13\Nosmi\Cache;

use Explt13\Nosmi\Base\Redis;
use Explt13\Nosmi\Interfaces\CacheInterface;
use Explt13\Nosmi\Interfaces\ConfigInterface;

class RedisCache implements CacheInterface
{
    protected ConfigInterface $config;
    protected Redis $redis;

    public function __construct(ConfigInterface $config, Redis $redis)
    {
        $this->config = $config;
        $this->redis = $redis;
    }

    public function set(string $key, $data, int $expires = 3600): bool
    {
        if ($expires <= 0) {
            throw new \RuntimeException("Cannot set cache $key with $expires seconds");
        }
        if (is_null($this->redis->client->set($key, serialize($data), 'EX', $expires))) {
            return false;
        }
        return true;
    }

    public function update(string $key, $data): bool
    {
        $left = $this->redis->client->ttl($key);
        if ($left === -2 || $left === -1) {
            return false;
        }
        $this->redis->client->set($key, serialize($data), 'EX', $left);
        return true;
    }
    
    public function get(string $key)
    {
        $data = $this->redis->client->get($key);
        return $data ? unserialize($data) : null;
    }

    public function delete(string $key): bool
    {
        if ($this->redis->client->del($key)) {
            return true;
        }
        return false;
    }

    public function has(string $key): bool
    {
        return $this->redis->client->exists($key) > 0;
    }

    public function expire(string $key, int $seconds): bool
    {
        $left = $this->redis->client->ttl($key);
        if ($left === -2 || $left === -1) {
            return false;
        }
        $expire = $left + $seconds;
        $this->redis->client->expire($key, $expire);
        return true;
    }

    public function getTtl(string $key): int
    {
        return $this->redis->client->ttl($key);
    }
}