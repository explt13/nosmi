<?php
namespace Explt13\Nosmi\Interfaces;

interface CacheFactoryInterface
{
    /**
     * Creates an instance of a file-based cache.
     *
     * @return CacheInterface An instance of a file-based cache.
     */
    public function createFileCache(): CacheInterface;
     
    /**
     * Creates an instance of a Redis-based cache.
     *
     * @return CacheInterface An instance of a Redis-based cache.
     */
    public function createRedisCache(): CacheInterface;

    /**
     * Creates an instance based on a config handler parameter
     *
     * @return CacheInterface An instance of cache.
     */
    public function createCacheBasedOnConfigHandler(): CacheInterface;
}