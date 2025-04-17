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
    private ConfigLoader $config_loader;
    private bool $bootstrapped = false;

    public function __construct(
        DependencyManagerInterface $dependency_manager,
        ConfigLoader $config_loader
    )
    {
        $this->dependency_manager = $dependency_manager;
        $this->config_loader = $config_loader;
    }

    public function bootstrap(
        ?string $config_path = null,
        ?string $dependencies_path = null
    ): void
    {
        // Define framework's root folder path constant
        define('FRAMEWORK', dirname(__DIR__));
        // Load app's config
        $this->config_loader->loadConfig($config_path);
        // Load framework's dependencies
        $this->dependency_manager->loadDependencies(FRAMEWORK . '/Config/dependencies.php');
        // Load app's dependencies
        $this->dependency_manager->loadDependencies($dependencies_path);

        // ErrorHandler::getInstance();
        // $serviceLoader = $this->dependency_manager->getDependency(ServiceProviderLoader::class);
        // $serviceLoader->load();
        $this->bootstrapped = true;
    }

    public function run(): void
    {
        if (!$this->bootstrapped) {
            // Log critical
            throw new \Exception('The app hasn\'t been bootstrapped, call App::bootstrap method first');
        }

        session_start();
        $router = $this->dependency_manager->getDependency(Router::class);
        $router->dispatch(Request::init());
    }
}
