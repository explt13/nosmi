<?php

namespace Tests\unit;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\AppConfig\ConfigLoader;
use PHPUnit\Framework\TestCase;

class AppConfigTest extends TestCase
{
    private AppConfig $app_config;

    public function testLoadConfig()
    {
        
    }

    public function setUp(): void
    {
        $this->app_config = AppConfig::getInstance();
        

    }
}