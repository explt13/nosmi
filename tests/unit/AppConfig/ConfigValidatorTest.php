<?php

namespace Tests\Unit\AppConfig;

use Explt13\Nosmi\AppConfig\ConfigValidator;
use Explt13\Nosmi\Exceptions\ArrayNotAssocException;
use Explt13\Nosmi\Exceptions\MissingAssocArrayKeyException;
use Explt13\Nosmi\Exceptions\SetReadonlyException;
use Explt13\Nosmi\Interfaces\ConfigValidatorInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Unit\helpers\IncludeFiles;

class ConfigValidatorTest extends TestCase
{
    private ConfigValidatorInterface $config_validator;

    public static function setUpBeforeClass(): void
    {
        IncludeFiles::includeUtilFunctions();
    }

    protected function setUp(): void
    {
        $this->config_validator = new ConfigValidator();
    }

    public static function readonlyProvider()
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

    public static function attributesProvider()
    {
        return [
            "valid attributes" => [
                "name" => "valid",
                "attributes" => [
                    "one" => 2,
                    "two" => "three",
                    "three" => true,
                    "four" => null,
                    "five" => [1, 2, 3],
                    "six" => [
                        "a" => "b",
                        "1" => 2,
                    ]
                ]
            ],
            "invalid attributes 1" => [
                "name" => "invalid",
                "attributes" => [
                    1,
                    2,
                    3
                ]
            ],
            "invalid attributes 2" => [
                "name" => "invalid",
                "attributes" => [
                    "a" => "b",
                    "c" => 2,
                    1 => "asd",
                ]
            ]
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

    public static function removableProvider()
    {
        return [
            "removable set true" => [
                "parameter" => [
                    "removable" => true,
                    "value" => 0,
                ]
            ],
            "removable set false" => [
                "parameter" => [
                    "removable" => false,
                    "value" => 1,
                ]
            ],
            "removable not set" => [
                "parameter" => [
                    "value" => 2
                ]
            ],
        ];
    }

    #[DataProvider('readonlyProvider')]
    public function testReadonly(array $parameter)
    {
        if (($parameter['readonly'] ?? false) === true) {
            $this->expectException(SetReadonlyException::class);
            $is_readonly = $this->config_validator->isReadonly($parameter);
            $this->assertTrue($is_readonly);
        }
        $this->config_validator->checkReadonly('parameter', $parameter);
        $not_readonly = $this->config_validator->isReadonly($parameter);
        $this->assertFalse($not_readonly);
    }

    #[DataProvider('complexParameterProvider')]
    public function testIsComplexParameter(mixed $parameter)
    {
        $is_complex = $this->config_validator->isComplexParameter($parameter);
        if (is_array($parameter)) {
            $this->assertTrue($is_complex);
            return;
        }
        $this->assertFalse($is_complex);
    }

    #[DataProvider('attributesProvider')]
    public function testValidateAttributes(string $name, array $attributes)
    {
        if ($name === "invalid") {
            $this->expectException(ArrayNotAssocException::class);
        }
        $this->config_validator->validateAttributes($name, $attributes);
        $this->assertTrue(true);
    }

    #[DataProvider('hasValueProvider')]
    public function testHasValue(array $parameter)
    {
        if (!array_key_exists('value', $parameter)) {
            $this->expectException(MissingAssocArrayKeyException::class);
        }
        $this->config_validator->validateParameterHasRequiredAttribute(time(), $parameter, 'value');
        $this->assertTrue(true);
    }

    #[DataProvider('removableProvider')]
    public function testIsRemovable($parameter)
    {
        $removable = $this->config_validator->isRemovable($parameter);
        if (!isset($parameter['removable']) || $parameter['removable'] === true) {
            $this->assertTrue($removable);
            return;
        }
        $this->assertFalse($removable);
    }
}