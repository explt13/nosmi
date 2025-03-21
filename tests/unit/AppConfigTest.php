<?php

namespace Tests\unit;

use Explt13\Nosmi\AppConfig;
use PHPUnit\Framework\TestCase;

class AppConfigTest extends TestCase
{
    private AppConfig $app_config;

    public function testLoadConfig()
    {
        print_r($this->app_config->getAll());
    }

    public function setUp(): void
    {
        $this->app_config = AppConfig::getInstance();
    }
}