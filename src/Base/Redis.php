<?php

namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Traits\SingletonTrait;
use Predis\Client;

class Redis
{
    use SingletonTrait;
    public Client $client;

    protected function __construct()
{
    $config = AppConfig::getInstance();
    try {
        $this->client = new Client(array(
            'scheme'              => $config->get("REDIS_SCHEME"),
            'host'                => $config->get("REDIS_HOSTNAME"),
            'port'                => $config->get("REDIS_PORT"),
            'database'            => $config->get("REDIS_DATABASE"),
            'username'            => $config->get("REDIS_USERNAME"),
            'password'            => $config->get("REDIS_PASSWORD"),
            'path'                => $config->get("REDIS_PATH"),
            'async'               => $config->get("REDIS_ASYNC"),
            'timeout'             => $config->get("REDIS_TIMEOUT"),
            'read_write_timeout'  => $config->get("REDIS_READ_WRITE_TIMEOUT"),
        ));

        // Validate connection
        if (!$this->client->ping()) {
            throw new \RuntimeException("Redis connection failed.");
        }
    } catch (\Exception $e) {
        throw new \RuntimeException("Failed to initialize Redis client: " . $e->getMessage());
    }
}
}