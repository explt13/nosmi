<?php
namespace Tests\Integration\App;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\AppConfig\ConfigLoader;
use Explt13\Nosmi\Base\App;
use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Dependencies\DependencyManager;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\DependencyManagerInterface;
use Explt13\Nosmi\Routing\Router;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    private App $app;
    private ConfigInterface $app_config;
    private DependencyManagerInterface $dependency_manager;

    public function setUp(): void
    {
        $this->dependency_manager = new DependencyManager(Container::getInstance());
        $this->app_config = AppConfig::getInstance();
        $this->app = new App($this->dependency_manager, $this->app_config, new ConfigLoader($this->app_config));
    }

    public function testBootstrap()
    {
        $this->app->bootstrap();
        $this->assertSame('/var/www/packages/nosmi/src', FRAMEWORK);
        $this->assertTrue($this->dependency_manager->hasDependency(Router::class));
        // require_once ($this->app_config->get('FRAMEWORK'). '/Cache/Cache.php');
    }
}