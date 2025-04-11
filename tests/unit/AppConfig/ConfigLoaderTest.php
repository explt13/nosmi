<?php

namespace Tests\Unit\AppConfig;

use Dotenv\Dotenv;
use Explt13\Nosmi\AppConfig\ConfigLoader;
use Explt13\Nosmi\Exceptions\InvalidFileExtensionException;
use Explt13\Nosmi\Exceptions\InvalidResourceException;
use Explt13\Nosmi\Exceptions\ResourceNotFoundException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\FileValidatorInterface;
use Explt13\Nosmi\Validators\FileValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\helpers\IncludeFiles;

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
        Dotenv::createImmutable(dirname(__FILE__, 4) . '/src/Config')->load();
        $this->mock_app_config
             ->expects(($this->once()))
             ->method('bulkSet')
             ->with($_ENV);

        $this->config_loader->loadConfig(dirname(__FILE__, 4) . '/src/Config/.env');
    }

    public static function loadUserConfigProvider()
    {
        return [
            "json config" => [
                "ext" => "json",
                "path" => __DIR__ . '/mockdata/user_config.json',
            ],
            "ini config" => [
                "ext" => "ini",
                "path" => __DIR__ . '/mockdata/user_config.ini'
            ],
            "env config" => [
                "ext" => "env",
                "path" => __DIR__ . '/mockdata/.env'
            ],
            "directory provided" => [
                "ext" => "none",
                "path" => __DIR__ . '/mockdata',
                "fail" => "directory provided"
            ],
            "not supported extension" => [
                "ext" => "xml",
                "path" => __DIR__ . '/mockdata/config.xml',
                "fail" => "not supported extension"
            ],
            "not existed file" => [
                "ext" => "json",
                "path" => __DIR__ . '/mockdata/not_existed.json',
                "fail" => "not existed file"
            ],
            "not existed directory" => [
                "ext" => "none",
                "path" => __DIR__ . '/not_existed',
                "fail" => "not existed directory"
            ]
        ];
    }

    #[DataProvider('loadUserConfigProvider')]
    public function testLoadUserConfig($ext, $path, $fail = null)
    {
        if (is_null($fail)) {
            $invocation_mocker = $this->mock_app_config
            ->expects(($this->once()))
            ->method('bulkSet');
       
            switch ($ext) {
                case "json":
                    $invocation_mocker->with(json_decode(file_get_contents($path), true));
                    break;
                case "ini":
                    $invocation_mocker->with(parse_ini_file($path));
                    break;
                case "env":
                    Dotenv::createImmutable(dirname($path))->load();
                    $invocation_mocker->with($_ENV);
                    break;
            }
        }
        
        if (!is_null($fail)) {
            if ($fail === 'directory provided') {
                $this->expectException(InvalidResourceException::class);
            }
            if ($fail === "not supported extension") {
                $this->expectException(InvalidFileExtensionException::class);
            }
            if ($fail === "not existed file") {
                $this->expectException(ResourceNotFoundException::class);
            }
            if ($fail === "not existed directory") {
                $this->expectException(ResourceNotFoundException::class);
            }
        }

        $this->config_loader->loadConfig($path);

    }

    public function testLoadJsonUserConfig()
    {
        $config_path = __DIR__ . '/mockdata/user_config.json';
        $this->mock_app_config
             ->expects(($this->once()))
             ->method('bulkSet')
             ->with(json_decode(file_get_contents($config_path), true));
        $this->config_loader->loadConfig($config_path);


    }

    public function testLoadIniUserConfig()
    {
        $config_path = __DIR__ . '/mockdata/user_config.ini';
        $this->mock_app_config
             ->expects(($this->once()))
             ->method('bulkSet')
             ->with(parse_ini_file($config_path));
        $this->config_loader->loadConfig($config_path);
    }

    public function testLoadEnvUserConfig()
    {
        $config_path = __DIR__ . '/mockdata/.env';
        Dotenv::createImmutable(dirname($config_path))->load();
        $this->mock_app_config
             ->expects(($this->once()))
             ->method('bulkSet')
             ->with($_ENV);
        $this->config_loader->loadConfig($config_path);
    }
}