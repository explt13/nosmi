<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\AppConfig\ConfigLoader;
use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Interfaces\DependencyManagerInterface;
use Explt13\Nosmi\Routing\Request;
use Explt13\Nosmi\Routing\Router;

class App
{
    private DependencyManagerInterface $dependency_manager;
    private ConfigInterface $app_config;
    private ConfigLoader $config_loader;
    private bool $bootstraped = false;

    public function __construct(
        DependencyManagerInterface $dependency_manager,
        ConfigInterface $app_config,
        ConfigLoader $config_loader
    )
    {
        $this->dependency_manager = $dependency_manager;
        $this->app_config = $app_config;
        $this->config_loader = $config_loader;
    }

    private function bootstrap(): void
    {
        
        define('FRAMEWORK', dirname(__DIR__));
        $this->config_loader->loadConfig($this->config_loader::DEFAULT_FRAMEWORK_CONFIG_PATH);
        $this->dependency_manager->loadDependencies(FRAMEWORK . '/Config/dependencies.php');
        // ErrorHandler::getInstance();
        // $serviceLoader = $this->dependency_manager->getDependency(ServiceProviderLoader::class);
        // $serviceLoader->load();
        $this->bootstraped = true;
    }

    public function run(): void
    {
        if (!$this->bootstraped) {
            $this->bootstrap();
        }
        session_start();
        $router = $this->dependency_manager->getDependency(Router::class);
        $router->dispatch(Request::init());
    }
}
