<?php

namespace Explt13\Nosmi\Utils;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Interfaces\ConfigInterface;

class Namespacer
{
    private ConfigInterface $config;
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }
    
    /**
     * Generate a namespace for the target directory
     * @param string $target_directory the directory to generate namespace for
     */
    public function generateNamespace(string $target_directory): string
    {
        return $this->config->get('APP_PSR') . str_replace(
            '/', 
            '\\', 
            ltrim(str_replace($this->config->get('APP_SRC'), '', $target_directory), '/')) . '\\';
    }

}