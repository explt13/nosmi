<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Dependencies\DependencyManager;
use Explt13\Nosmi\Http\ServerRequest;
use Explt13\Nosmi\Interfaces\AppInterface;
use Explt13\Nosmi\Interfaces\ConfigLoaderInterface;
use Explt13\Nosmi\Interfaces\MiddlewareRegistryInterface;
use Explt13\Nosmi\Interfaces\RouterInterface;
use Explt13\Nosmi\Routing\Router;

class App implements AppInterface
{
    private MiddlewareRegistryInterface $middleware_registry;
    private Router $router;
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

    public function registerService(string $service): void
    {

    }

    public function bootstrap(string $config_path): void
    {
        // Define framework's root folder path constant
        define('FRAMEWORK', dirname(__DIR__));
        
        // create dependency manager
        $dependency_manager = new DependencyManager();

        // Load framework's dependencies
        $dependency_manager->loadFrameworkDependencies(FRAMEWORK . '/Config/dependencies.php');

        // get config loader object
        $config_loader = $dependency_manager->getDependency(ConfigLoaderInterface::class);
        
        // Load app's config
        $config_loader->loadConfig($config_path);

        // Set router
        $this->router = $dependency_manager->getDependency(RouterInterface::class);

        // Set middleware registry
        $this->middleware_registry = $dependency_manager->getDependency(MiddlewareRegistryInterface::class);


        $this->bootstrapped = true;
    }

    public function run(): void
    {
        $this->assureBootstrap();
        session_start();
        $request = ServerRequest::capture();
        $route = $this->router->resolve($request);
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
