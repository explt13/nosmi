<?php

namespace Explt13\Nosmi\Interfaces;

interface ConfigLoaderInterface
{
    /**
     * Load a config in .env, .json, .ini
     * @param string $config_path a destination to the config file
     * @return void
     */
    public function loadConfig(string $config_path): void;
}