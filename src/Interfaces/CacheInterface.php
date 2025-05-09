<?php

namespace Explt13\Nosmi\Interfaces;

interface CacheInterface
{
    /**
     * Stores data in the cache with a specified key and expiration time.
     *
     * @param string $key The unique key to identify the cached data.
     * @param mixed $data The data to be stored in the cache.
     * @param int $expires The time in seconds until the cache expires. Default is 3600 seconds (1 hour).
     * @return bool Returns true if the data was successfully stored, false otherwise.
     */
    public function set(string $key, $data, int $expires = 3600): bool;

    /**
     * Updates the cache with the given key and data.
     *
     * @param string $key The unique identifier for the cache entry.
     * @param mixed $data The data to be stored in the cache.
     * @return bool Returns true if the cache was successfully updated, false otherwise.
     */
    public function update(string $key, $data): bool;
     
    /**
     * Retrieves data from the cache using the specified key.
     *
     * @param string $key The unique key to identify the cached data.
     * @return mixed Returns the cached data if the key exists, or null if the key does not exist.
     */
    public function get(string $key);
     
    /**
     * Deletes data from the cache using the specified key.
     *
     * @param string $key The unique key to identify the cached data.
     * @return bool Returns true if the data was successfully deleted, false otherwise.
     */
    public function delete(string $key): bool;
     
    /**
     * Checks if a specific key exists in the cache.
     *
     * @param string $key The unique key to identify the cached data.
     * @return bool Returns true if the key exists in the cache, false otherwise.
     */
    public function has(string $key): bool;
     
    /**
     * Updates the expiration time for a specific key in the cache.
     *
     * @param string $key The unique key to identify the cached data.
     * @param int $seconds The new expiration time in seconds.
     * @return bool Returns true if the expiration time was successfully updated, false otherwise.
     */
    public function expire(string $key, int $seconds): bool;

    /**
     * Gets ttl for a specific key in the cache
     * @param string $key The unique key to identify the cached data.
     * @return int time to live left
     */
    public function getTtl(string $key): int;
}