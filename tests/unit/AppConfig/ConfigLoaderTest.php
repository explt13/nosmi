<?php

namespace Tests\Unit\AppConfig;

use Dotenv\Dotenv;
use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\AppConfig\ConfigInterface;
use Explt13\Nosmi\AppConfig\ConfigLoader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\helpers\IncludeFiles;
use Tests\Unit\helpers\SingletonReset;

class ConfigLoaderTest extends TestCase
{
    private (ConfigInterface&MockObject)|null $mock_app_config;
    private ConfigLoader|null $config_loader;

    public static function setUpBeforeClass(): void
    {
        IncludeFiles::includeUtilFunctions();
    }
    public function setUp(): void
    {
        $this->mock_app_config = $this->createMock(ConfigInterface::class);
        $this->config_loader = new ConfigLoader($this->mock_app_config);
    }

    public function tearDown(): void
    {
        $this->mock_app_config = null;
        $this->config_loader = null;
    }

    public function testLoadFrameworkConfig()
    {
        $this->mock_app_config
             ->expects(($this->once()))
             ->method('bulkSet')
             ->with(
                json_decode(file_get_contents(dirname(__FILE__, 4) . '/src/Config/default_config.json'), true)
            );
        // Loads default config on creation 
        $loader = new ConfigLoader($this->mock_app_config);
        unset($loader);
    }

    public function testLoadJsonUserConfig()
    {
        $config_path = __DIR__ . '/mockdata/user_config.json';
        $this->mock_app_config
             ->expects(($this->once()))
             ->method('bulkSet')
             ->with(json_decode(file_get_contents($config_path), true));
        $this->config_loader->loadUserConfig($config_path);
    }

    public function testLoadIniUserConfig()
    {
        $config_path = __DIR__ . '/mockdata/user_config.ini';
        $this->mock_app_config
             ->expects(($this->once()))
             ->method('bulkSet')
             ->with(parse_ini_file($config_path));
        $this->config_loader->loadUserConfig($config_path);
    }

    public function testLoadEnvUserConfig()
    {
        $config_path = __DIR__ . '/mockdata/.env';
        Dotenv::createImmutable(dirname($config_path))->load();
        $this->mock_app_config
             ->expects(($this->once()))
             ->method('bulkSet')
             ->with($_ENV);
        $this->config_loader->loadUserConfig($config_path);
    }
}