<?php

namespace Tests\Unit\AppConfig;

use Dotenv\Dotenv;
use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\AppConfig\ConfigLoader;
use Explt13\Nosmi\Exceptions\InvalidFileExtensionException;
use Explt13\Nosmi\Exceptions\InvalidResourceException;
use Explt13\Nosmi\Exceptions\ResourceNotFoundException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Validators\FileValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\helpers\IncludeFiles;
use Tests\Unit\helpers\Reset;

class ConfigLoaderTest extends TestCase
{
    private (ConfigInterface&MockObject)|null $mock_app_config;
    private FileValidator&MockObject $file_validator;
    private ConfigLoader|null $config_loader;

    public function setUp(): void
    {
        $this->mock_app_config = $this->createMock(ConfigInterface::class);
        $this->mock_app_config->method('HAS')->willReturn(true);
        $this->mock_app_config->method('GET')->willReturnCallback(function($param) {
            if ($param === 'APP_ROUTES_FILE' || $param === 'APP_DEPENDENCIES_FILE') {
                return __FILE__;
            }
            return __DIR__;
        });
        $this->file_validator = $this->createMock(FileValidator::class);
        $this->file_validator->method('isReadableDir')->willReturn(true);
       
        $this->config_loader = new ConfigLoader($this->mock_app_config);
    }

    public function tearDown(): void
    {
        $this->mock_app_config = null;
        $this->config_loader = null;
    }


    public static function loadConfigProvider()
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
            // "env config" => [
            //     "ext" => "env",
            //     "path" => __DIR__ . '/mockdata/.env'
            // ],
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

    #[DataProvider('loadConfigProvider')]
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
                $this->file_validator->method('isFile')->willReturn(false);
                $this->expectException(InvalidResourceException::class);
            }
            if ($fail === "not supported extension") {
                $this->file_validator->method('isValidExtension')->willReturn(false);
                $this->expectException(InvalidFileExtensionException::class);
            }
            if ($fail === "not existed file") {
                $this->file_validator->method('resourceExists')->willReturn(false);
                $this->expectException(ResourceNotFoundException::class);
            }
            if ($fail === "not existed directory") {
                $this->file_validator->method('resourceExists')->willReturn(false);
                $this->expectException(ResourceNotFoundException::class);
            }
        }

        $this->file_validator->method('isReadableDir')->willReturn(true);
        $this->file_validator->method('resourceExists')->willReturn(true);
        $this->file_validator->method('isFile')->willReturn(true);
        $this->file_validator->method('isValidExtension')->willReturn(true);
        $this->file_validator->method('isReadable')->willReturn(true);
        $this->config_loader->loadConfig($path);

    }
}