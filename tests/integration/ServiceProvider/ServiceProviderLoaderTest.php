<?php

namespace Tests\Integration\ServiceProvider;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Base\ServiceProviderLoader;
use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Exceptions\ConfigParameterNotSetException;
use Explt13\Nosmi\Exceptions\InvalidResourceException;
use Explt13\Nosmi\Exceptions\InvalidTypeException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Utils\Namespacer;
use Explt13\Nosmi\Validators\FileValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Integration\ServiceProvider\fakeapp\src\providers\FakeProvider;
use Tests\Integration\ServiceProvider\fakeapp\src\something\custom\CustomFakeProvider;
use Tests\Integration\ServiceProvider\fakeapp\src\something\providers\InvalidProvider;

class ServiceProviderLoaderTest extends TestCase
{
    private ServiceProviderLoader $spl;
    private ConfigInterface $config;
    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->config = AppConfig::getInstance();
        $this->container = Container::getInstance();
        $this->config->set('APP_ROOT', __DIR__ . '/fakeapp');
        $this->config->set('APP_PSR', 'Tests\\Integration\\ServiceProvider\\fakeapp\\src\\');
        $this->container->set(FakeProvider::class, FakeProvider::class);
        $this->container->set(CustomFakeProvider::class, CustomFakeProvider::class);
        $this->container->set(InvalidProvider::class, InvalidProvider::class);

        $this->config->set('APP_SRC', $this->config->get('APP_ROOT') . '/src');
        $this->spl = new ServiceProviderLoader(
            $this->container, 
            $this->config, 
            new Namespacer($this->config),
            new FileValidator(), 
        );
    }

    public static function loadProvider(): array
    {
        return [
            "provider with default path" => [
                "provider" => FakeProvider::class,
                "expected_message" => FakeProvider::class . ' has been booted',
                "app_providers" => null,
            ],
            "provider with custom path" => [
                "provider" => CustomFakeProvider::class,
                "expected_message" => CustomFakeProvider::class . " custom pathed provider has been booted",
                "app_providers" => __DIR__ . '/fakeapp/src/something/custom',
            ]
        ];
    }

    #[DataProvider('loadProvider')]
    public function testLoad($provider, $expected_message, $app_providers)
    {
        $this->config->set('APP_PROVIDERS', $app_providers);
        $this->expectOutputString($expected_message);
        $this->spl->load();
    }

    public static function configProvider(): array
    {
        return [
            "non-existed root" => [
                "parameter" => "APP_SRC",
                "value" => __DIR__ . '/src/not_existed',
                "should_throw" => InvalidResourceException::class,
            ],
            "non-existed providers" => [
                "parameter" => "APP_PROVIDERS",
                "value" => __DIR__ . '/src/not_existed_providers',
                "should_throw" => InvalidResourceException::class,
            ],
            "root config parameter not set" => [
                "parameter" => "APP_ROOT",
                "value" => null,
                "should_throw" => ConfigParameterNotSetException::class,
            ],
            "not implementing service provider interface" => [
                "parameter" => "APP_PROVIDERS",
                "value" => __DIR__ . '/fakeapp/src/something/providers',
                "should_throw" => InvalidTypeException::class,
            ],
        ];
    }


    #[DataProvider('configProvider')]
    public function testEdges($parameter, $value, $should_throw)
    {
        $this->config->set($parameter, $value);
        $this->expectException($should_throw);
        $this->spl->load();
    }
}