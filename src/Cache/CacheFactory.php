<?php

namespace Explt13\Nosmi\Cache;

use Explt13\Nosmi\Base\Redis;
use Explt13\Nosmi\Interfaces\CacheFactoryInterface;
use Explt13\Nosmi\Interfaces\CacheInterface;
use Explt13\Nosmi\Interfaces\ConfigInterface;

class CacheFactory implements CacheFactoryInterface
{
    private ConfigInterface $config;
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }
    
    public function createFileCache(): CacheInterface
    {
        return new FileCache($this->config);
    }

    public function createRedisCache(): CacheInterface
    {
        $redis = Redis::getInstance();
        return new RedisCache($this->config, $redis);
    }

    public function createCacheBasedOnConfigHandler(): CacheInterface
    {
        $cache_handler = $this->config->get('CACHE_HANDLER');
        switch ($cache_handler) {
            case 'redis':
                return $this->createRedisCache();
            case 'file':
                return $this->createFileCache();
            default:
                throw new \RuntimeException("Cannot create cache, undefined handler: $cache_handler");
        }
    }
}