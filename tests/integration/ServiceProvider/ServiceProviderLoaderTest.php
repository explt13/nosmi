<?php

namespace Tests\Integration\ServiceProvider;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\AppConfig\ConfigLoader;
use Explt13\Nosmi\Base\ServiceProviderLoader;
use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Dependencies\DependencyManager;
use Explt13\Nosmi\Exceptions\InvalidTypeException;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Interfaces\DependencyManagerInterface;
use Explt13\Nosmi\Interfaces\FileValidatorInterface;
use Explt13\Nosmi\Interfaces\ServiceProviderInterface;
use Explt13\Nosmi\Utils\Namespacer;
use Explt13\Nosmi\Validators\FileValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Integration\fakeapp\src\providers\FakeProvider;
use Tests\Integration\fakeapp\src\customproviders\custom\CustomFakeProvider;
use Tests\Integration\fakeapp\src\customproviders\providers\InvalidProvider;

class ServiceProviderLoaderTest extends TestCase
{
    private ServiceProviderLoader $spl;
    private ConfigInterface $config;
    private DependencyManagerInterface $dep_manager;

    public static function setUpBeforeClass(): void
    {
        $dm = new DependencyManager(Container::getInstance());
        $dm->loadDependencies('/var/www/packages/nosmi/src/Config/dependencies.php');
        $dm->addDependency(FileValidatorInterface::class, FileValidator::class, true);
    }
    
    protected function setUp(): void
    {
        $this->dep_manager = new DependencyManager(Container::getInstance());
        $this->config = AppConfig::getInstance();
        $fv1 = $this->dep_manager->getDependency(FileValidatorInterface::class);
        $loader = new ConfigLoader($this->config, $fv1);
        $loader->loadConfig(dirname(__DIR__) .'/fakeapp/config/.env');
        $this->dep_manager->addDependency(FakeProvider::class, FakeProvider::class);
        $this->dep_manager->addDependency(CustomFakeProvider::class, CustomFakeProvider::class);
        $this->dep_manager->addDependency(InvalidProvider::class, InvalidProvider::class);

        $this->config->set('APP_SRC', $this->config->get('APP_ROOT') . '/src');
        $this->spl = $this->dep_manager->getDependency(ServiceProviderLoader::class);
    }

    public static function loadProvider(): array
    {
        return [
            "provider with default path" => [
                "expected_message" => FakeProvider::class . ' has been booted',
            ],
            "provider with custom path" => [
                "expected_message" => CustomFakeProvider::class . " custom pathed provider has been booted",
                "app_providers" => dirname(__DIR__) . '/fakeapp/src/customproviders/custom',
            ],
            "not implementing service provider interface" => [
                "expected_message" => sprintf("Invalid type of object: expected type: %s, but got: %s", 
                    ServiceProviderInterface::class,
                    InvalidProvider::class,
                ),
                "app_providers" => dirname(__DIR__) . '/fakeapp/src/customproviders/providers',
                "exception" => InvalidTypeException::class
            ],
        ];
    }

    #[DataProvider('loadProvider')]
    public function testLoad($expected_message, $app_providers = null, $exception = false)
    {
        if ($exception) {
            $this->expectException($exception);
            $this->expectExceptionMessage($expected_message);
            $this->config->set('APP_PROVIDERS', $app_providers);
            $this->spl->load();
        }
        if (!is_null($app_providers)) {
            $this->config->set('APP_PROVIDERS', $app_providers);
        }
        $this->expectOutputString($expected_message);
        $this->spl->load();
    }
}