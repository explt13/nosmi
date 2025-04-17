<?php
namespace Explt13\Nosmi\Routing;

use Explt13\Nosmi\Base\ControllerResolver;
use Explt13\Nosmi\Base\MiddlewareLoader;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\LightRequestInterface;

class Router
{
    private array $routes;
    private string $routes_dest;
    private MiddlewareLoader $middleware_loader;
    private ControllerResolver $controller_resolver;
    private Route $route;
    private ConfigInterface $config;

    public function __construct(
        MiddlewareLoader $middleware_loader,
        ControllerResolver $controller_resolver,
        Route $route,
        ConfigInterface $config
    )
    {
        $this->routes_dest = $this->config->get("APP_CONFIG") . '/routes.php';
        $this->middleware_loader = $middleware_loader;
        $this->controller_resolver = $controller_resolver;
        $this->route = $route;
        $this->routes = require_once $this->routes_dest;
    }

    /**
     * Adds a middleware for request/response for __all__ routes
     * @param callable(Psr\Http\Message\LightRequestInterface $request, \Psr\Http\Message\LightResponseInterface $response):void $middleware the middleware to add
     * @return void
     */
    public function use(callable $middleware): void
    {
        $this->middleware_loader->add($middleware);
    }

    /**
     * Sets a custom dest to the routes, defualt is APP_ROOT/config/routes.php file
     * @param string $routes_dest a path to routes file that must return an array with routes where [pattern] => [default context, ...];
     * @return void
     */
    public function setRoutesDest(string $routes_dest): void
    {
        $this->routes_dest = $routes_dest;
    }

    /**
     * Returns all routes
     * @return array the array of routes
     */
    public function getRoutes(): array
    {   
        return $this->routes;
    }
    
    public function dispatch(LightRequestInterface $request): void
    {
        $route = $this->route->setRoute($request->getUri()->getPath());
        $this->controller_resolver->resolve();
    }
}