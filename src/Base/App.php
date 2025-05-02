<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Dependencies\DependencyManager;
use Explt13\Nosmi\Http\ServerRequest;
use Explt13\Nosmi\Interfaces\AppInterface;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\ConfigLoaderInterface;
use Explt13\Nosmi\Interfaces\MiddlewareRegistryInterface;
use Explt13\Nosmi\Interfaces\RequestPipelineInterface;
use Explt13\Nosmi\Interfaces\RouterInterface;

class App implements AppInterface
{
    private MiddlewareRegistryInterface $middleware_registry;
    private RouterInterface $router;
    private RequestPipelineInterface $request_pipeline;
    private bool $bootstrapped = false;

    /**
     * Adds a middleware for request/response for __all__ routes
     * @param string $middleware class name of the middleware (::class)
     * @return void
     */
    public function use(string $middleware): static
    {
        $this->assureBootstrap();
        $this->middleware_registry->add($middleware);
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

        // get config loader object
        $config_loader = $dependency_manager->getDependency(ConfigLoaderInterface::class);
        
        // Load app's config
        $config_loader->loadConfig($config_path);

        // Set router
        $this->router = $dependency_manager->getDependency(RouterInterface::class);

        // Set middleware registry
        $this->middleware_registry = $dependency_manager->getDependency(MiddlewareRegistryInterface::class);

        $this->request_pipeline = $dependency_manager->getDependency(RequestPipelineInterface::class);

        $app_config = $dependency_manager->getDependency(ConfigInterface::class);

        require_once $app_config->get('APP_ROUTES_FILE') ?? $app_config->get('APP_SRC') . '/routes/routes.php';
        $dependency_manager->loadDependencies($app_config->get('APP_DEPENDENCIES_FILE') ?? $app_config->get('APP_SRC') . '/dependencies/dependencies.php');

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
