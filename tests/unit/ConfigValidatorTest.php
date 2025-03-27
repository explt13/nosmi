<?php

namespace Tests\unit;

use Explt13\Nosmi\AppConfig\ConfigValidator;
use Explt13\Nosmi\AppConfig\ConfigValidatorInterface;
use Explt13\Nosmi\Exceptions\ArrayNotAssocException;
use Explt13\Nosmi\Exceptions\ConfigAttributeException;
use Explt13\Nosmi\Exceptions\SetReadonlyException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConfigValidatorTest extends TestCase
{
    private ConfigValidatorInterface $config_validator;
    
    protected function setUp(): void
    {
        require_once __DIR__ . '/../../src/Utils/functions.php';
        $this->config_validator = new ConfigValidator();
    }

    public static function readonlyCheckProvider()
    {
        return [
            "readonly set true" => [
                "parameter" => [
                    "readonly" => true,
                    "value" => 0,
                ]
            ],
            "readonly set false" => [
                "parameter" => [
                    "readonly" => false,
                    "value" => 1,
                ]
            ],
            "readonly not set" => [
                "parameter" => [
                    "value" => 2
                ]
            ],
        ];
    }

    public static function complexParameterProvider()
    {
        return [
            "not complex parameter" => [
                "parameter" => 1
            ],
            "complex parameter" => [
                "parameter" => [
                    "readonly" => false,
                    "value" => 1,
                ]
            ],
        ];
    }
    public static function hasValueProvider()
    {
        return [
            "complex parameter missing value" => [
                "parameter" => [
                    "level" => 2,
                    "surface" => false,
                ]
            ],
            "complex parameter with value" => [
                "parameter" => [
                    "readonly" => false,
                    "value" => 1,
                ]
            ],
        ];
    }

    #[DataProvider('readonlyCheckProvider')]
    public function testReadOnlyCheck($parameter)
    {
        if (($parameter['readonly'] ?? false) === true) {
            $this->expectException(SetReadonlyException::class);
        }
        $this->config_validator->checkReadonly('parameter', $parameter);
        $not_readonly = $this->config_validator->isReadonly($parameter);
        $this->assertFalse($not_readonly);
    }

    #[DataProvider('complexParameterProvider')]
    public function testIsComplexParameter($parameter)
    {
        $is_complex = $this->config_validator->isComplexParameter($parameter);
        if (is_array($parameter)) {
            $this->assertTrue($is_complex);
            return;
        }
        $this->assertFalse($is_complex);
    }

    #[DataProvider('hasValueProvider')]
    public function testHasValue($parameter)
    {
        if (!array_key_exists('value', $parameter)) {
            $this->expectException(ConfigAttributeException::class);
        }
        $this->config_validator->validateParameterHasValue(time(), $parameter);
        $this->assertTrue(true);
    }
}