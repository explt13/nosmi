<?php
namespace Explt13\Nosmi\Base;

use ArgumentCountError;
use Explt13\Nosmi\Dependencies\DependencyManager;
use Explt13\Nosmi\Http\ServerRequest;
use Explt13\Nosmi\Interfaces\AppInterface;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\ConfigLoaderInterface;
use Explt13\Nosmi\Interfaces\MiddlewareRegistryInterface;
use Explt13\Nosmi\Interfaces\RequestPipelineInterface;
use Explt13\Nosmi\Interfaces\RouterInterface;
use Explt13\Nosmi\Routing\RoutesLoader;
use Psr\Http\Server\MiddlewareInterface;

class App implements AppInterface
{
    private MiddlewareRegistryInterface $middleware_registry;
    private RouterInterface $router;
    private RequestPipelineInterface $request_pipeline;
    private bool $bootstrapped = false;

    
    public function use(MiddlewareInterface $middleware): static
    {
        $this->assureBootstrap();
        $this->middleware_registry->add($middleware);
        return $this;
    }

    public function disable(string $middleware_class): static
    {
        $this->assureBootstrap();
        $this->middleware_registry->remove($middleware_class);
        return $this;
    }

    public function bootstrap(string $config_path): static
    {
        // Define framework's root folder path constant
        define('FRAMEWORK', dirname(__DIR__));
        
        // create dependency manager
        $dependency_manager = new DependencyManager();

        // Load framework's dependencies
        $dependency_manager->loadFrameworkDependencies(FRAMEWORK . '/Dependencies/dependencies.php');

        // Get config loader object
        $config_loader = $dependency_manager->getDependency(ConfigLoaderInterface::class);
        
        // Load app's config
        $config_loader->loadConfig($config_path);

        // Initialize error handler
        new ErrorHandler();

        // Set router
        $this->router = $dependency_manager->getDependency(RouterInterface::class);

        // Set middleware registry
        $this->middleware_registry = $dependency_manager->getDependency(MiddlewareRegistryInterface::class);

        // Set request pipeline
        $this->request_pipeline = $dependency_manager->getDependency(RequestPipelineInterface::class);

        // Get app config
        $app_config = $dependency_manager->getDependency(ConfigInterface::class);

        // Load routes
        RoutesLoader::load($app_config->get('APP_ROUTES_FILE'));

        // Load app dependencies
        $dependency_manager->loadDependencies($app_config->get('APP_DEPENDENCIES_FILE'));

        // Flag bootstrapped to true
        $this->bootstrapped = true;
        
        return $this;
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
