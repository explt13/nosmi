<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\AppConfig\ConfigLoader;
use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Dependencies\DependencyManager;
use Explt13\Nosmi\Http\ServerRequest;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\ConfigLoaderInterface;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Interfaces\DependencyManagerInterface;
use Explt13\Nosmi\Middleware\MiddlewareRegistry;
use Explt13\Nosmi\Routing\Router;

class App
{
    private DependencyManagerInterface $dependency_manager;
    private ConfigLoader $config_loader;
    private MiddlewareRegistry $middleware_registry;
    private RequestPipeline $request_pipeline;
    private bool $bootstrapped = false;


    /**
     * Adds a middleware for request/response for __all__ routes
     * @param string $middleware
     * Middleware to add, if string provided the name of the middleware __(not ::class)__ will be used and searched in specified __APP_MIDDLEWARES__ folder. \
     * @return void
     */
    public function use(string $middleware): void
    {
        $this->assureBootstrap();
        $this->middleware_registry->add($middleware);
    }


    public function bootstrap(string $config_path): void
    {
        // Define framework's root folder path constant
        define('FRAMEWORK', dirname(__DIR__));
        
        // create dependency manager
        $dependency_manager = new DependencyManager();

        // Load framework's dependencies
        $this->dependency_manager->loadFrameworkDependencies(FRAMEWORK . '/Config/dependencies.php');

        // get config loader object
        $config_loader = $dependency_manager->getDependency(ConfigLoaderInterface::class);
        
        // Load app's config
        $config_loader->loadConfig($config_path);

        // ErrorHandler::getInstance();
        // $serviceLoader = $this->dependency_manager->getDependency(ServiceProviderLoader::class);
        // $serviceLoader->load();
        $this->bootstrapped = true;
    }

    public function run(): void
    {
        $this->assureBootstrap();
        session_start();
        $request = ServerRequest::capture();
        $router = $this->dependency_manager->getDependency(Router::class);
        $route = $router->resolve($request);
        $response = $this->request_pipeline->process($request, $route);
        $response->send();
    }

    private function assureBootstrap()
    {
        if (!$this->bootstrapped) {
            // Log critical
            throw new \Exception('The app hasn\'t been bootstrapped, call App::bootstrap method first');
        }
    }
}
