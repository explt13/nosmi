<?php
namespace Explt13\Nosmi;


class Router
{
    private array $routes;
    private string $routes_dest;
    private MiddlewareLoader $middleware_loader;
    private ControllerResolver $controller_resolver;
    private RouteContext $route_context;

    public function __construct(MiddlewareLoader $middleware_loader, ControllerResolver $controller_resolver, RouteContext $route_context)
    {
        $this->routes_dest = CONF . '/routes.php';
        $this->middleware_loader = $middleware_loader;
        $this->controller_resolver = $controller_resolver;
        $this->route_context = $route_context;
        $this->routes = $this->getRoutes();
    }

    public function add(string $regexp, array $route = []): self
    {
        $this->routes[$regexp] = $route;
        return $this;
    }

    /**
     * @param string $routes_dest a path to routes file that must return an array with routes where [pattern] => [default params, ...];
     */
    public function setRoutesDest(string $routes_dest): void
    {
        $this->routes_dest = $routes_dest;
    }

    private function getRoutes(): array
    {   
        return require_once $this->routes_dest;
    }
    
    public function dispatch(string $url): void
    {
        $route = $this->extractRouteFromQueryString($url);
        $route_params = $this->getRouteParams($route);
        $this->route_context->setRoute($route_params);
        $this->middleware_loader->run();
        $this->controller_resolver->resolve();
    }

    /**
     * @var string $route
     * @return array route params [...default_params, ...$concrete_params];
     * @throws \Exception
     */
    private function getRouteParams(string $route): array
    {
        foreach ($this->routes as $pattern => $default_params) {
            if (preg_match("#{$pattern}#", $route, $matches)) {
                $matches = array_filter($matches, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
                return [...$default_params, ...$matches];
            }
        }
        throw new \Exception("Route `$route` is not found", 404);
    }

    private function extractRouteFromQueryString(string $url): string
    {
        if ($url) {
            $route = explode("&", $url, 2)[0];
            if (strpos($route, "=") === false) {
                return rtrim($route, '/');
            }
        }
        return '';
    }
}