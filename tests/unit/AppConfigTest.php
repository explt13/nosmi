<?php

namespace Tests\unit;

use Explt13\Nosmi\AppConfig;
use PHPUnit\Framework\TestCase;

class AppConfigTest extends TestCase
{
    private AppConfig $app_config;

    public function testLoadConfig()
    {
        $this->app_config->loadUserConfig(__DIR__ . '/mockdata/AppConfig/user_config.json');
        print_r($this->app_config->getAll());
        $this->assertTrue(true);
    }

    public function setUp(): void
    {
        $this->app_config = AppConfig::getInstance();
        

    }
}