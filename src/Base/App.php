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
    private MiddlewareLoader $middleware_loader;


    public function __construct(
        MiddlewareLoader $middleware_loader,
        DependencyManagerInterface $dependency_manager,
        ConfigLoader $config_loader
    )
    {
        $this->middleware_loader = $middleware_loader;
        $this->dependency_manager = $dependency_manager;
        $this->config_loader = $config_loader;
    }

    /**
     * Adds a middleware for request/response for __all__ routes
     * @param string $middleware
     * Middleware to add, if string provided the name of the middleware __(not ::class)__ will be used and searched in specified __APP_MIDDLEWARES__ folder. \
     * @return void
     */
    public function use(string $middleware): void
    {
        $this->middleware_loader->add($middleware);
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
