<?php

namespace Tests\Unit\AppConfig;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Exceptions\RemoveConfigParameterException;
use Explt13\Nosmi\Exceptions\SetReadonlyException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Unit\helpers\IncludeFiles;
use Tests\Unit\helpers\Reset;

class AppConfigTest extends TestCase
{
    private ConfigInterface $app_config;

    public function setUp(): void
    {
        $this->app_config = AppConfig::getInstance();
    }

    public function tearDown(): void
    {
        Reset::resetSingleton($this->app_config::class);
    }

    public static function setUpBeforeClass(): void
    {
        IncludeFiles::includeUtilFunctions();
    }

    public function testGetAllBulkSet()
    {
        $this->app_config->bulkSet([
            "primitive" => 1,
            "array" => [1, 2, 3],
            "complex" => [
                "value" => 15,
                "readonly" => true
            ]
        ]);

        $expected = [
            "primitive" => [
                "value" => 1,
                "readonly" => false
            ],
            "array" => [
                "value" => [1, 2, 3],
                "readonly" => false
            ],
            "complex" => [
                "value" => 15,
                "readonly" => true
            ]
        ];

        $this->assertSame($expected, $this->app_config->getAll());
    }

    public static function setProvider()
    {
        return [
            "basic" => [
                "basic prop",
                12
            ],
            "with null" => [
                "with null",
                null
            ],
            "with readonly" => [
                "with readonly prop",
                "some value",
                true
            ],
            "with extra attributes" => [
                "extra",
                true,
                false,
                [
                    "extra1" => false,
                    "extra2" => 13,
                ]
            ]
        ];
    }

    #[DataProvider('setProvider')]
    public function testGetSet(string $name, mixed $value, bool $readonly = false, array $attributes=[])
    {
        $expected = [
            "value" => $value,
            "readonly" => $readonly,
            ...$attributes
        ];
        $this->app_config->set($name, $value, $readonly, $attributes);
        if ($readonly === true) {
            $this->expectExceptionMessage("Cannot set/modify a read-only parameter: " . $name);
            $this->expectException(SetReadonlyException::class);
            $this->app_config->set($name, 'a new value', $readonly, $attributes);
        }
        $this->assertSame($expected, $this->app_config->get($name, true));
        $this->assertSame($value, $this->app_config->get($name));
    }

    public function testGetNotExisted()
    {
        $this->assertSame(null, $this->app_config->get('not_existed'));
    }
    
    public function testHas()
    {
        $this->app_config->set('a', 12);
        $this->app_config->set('b', "abc");
        $this->app_config->set('c', false);
        $this->app_config->set('d', null);

        $this->assertTrue($this->app_config->has("a"));
        $this->assertTrue($this->app_config->has("c"));
        $this->assertTrue($this->app_config->has('d'));
        $this->assertFalse($this->app_config->has("r"));
    }

    public function testRemove()
    {
        $this->app_config->set('a', 12);
        $this->assertTrue($this->app_config->has('a'));
        $this->assertTrue($this->app_config->remove('a'));
        $this->assertFalse($this->app_config->remove('a'));
        $this->assertFalse($this->app_config->has('a'));
        $this->app_config->set('try remove me', "123", false, ["removable" => false]);
        $this->expectException(RemoveConfigParameterException::class);
        $this->expectExceptionMessage('Failed to remove config parameter "try remove me": removable parameter');
        $this->app_config->remove('try remove me');
    }
}