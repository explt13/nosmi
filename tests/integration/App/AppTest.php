<?php
namespace Tests\Integration\App;

use Explt13\Nosmi\Base\App;
use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Dependencies\DependencyManager;
use Explt13\Nosmi\Interfaces\DependencyManagerInterface;
use Explt13\Nosmi\Interfaces\RouterInterface;
use Explt13\Nosmi\Routing\Router;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    private App $app;
    private DependencyManagerInterface $dependency_manager;

    public function setUp(): void
    {
        $this->app = new App();
    }

    public function testBootstrap()
    {
        $this->app->bootstrap(__DIR__ . '/../mockapp/config/.env');

        $this->assertSame('/var/www/packages/nosmi/src', FRAMEWORK);
        $this->dependency_manager = new DependencyManager();
        $this->assertTrue($this->dependency_manager->hasDependency(RouterInterface::class));
    }

    // public static function tearDownAfterClass(): void
    // {
    //     Reset::resetSingleton(Container::class);
    //     Reset::resetSingleton(AppConfig::class);
    // }
}