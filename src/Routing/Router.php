<?php
namespace Explt13\Nosmi\Routing;

use Explt13\Nosmi\Base\ControllerResolver;
use Explt13\Nosmi\Interfaces\LightFactoryInterface;
use Explt13\Nosmi\Interfaces\LightRequestInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;

class Router
{
    private LightFactoryInterface $factory;
    private LightRouteInterface $route;

    public function __construct(
        LightFactoryInterface $factory,
        LightRouteInterface $route,
    )
    {
        $this->factory = $factory;
        $this->route = $route;
    }

    public function dispatch(LightRequestInterface $request): void
    {
        if (empty($this->route::getRoutes())) {
            throw new \LogicException('No routes found, make sure you added them correctly.');
        }
        $path = $request->getUri()->getPath();
        $is_route_resolved = $this->route->resolvePath($path);
        if (!$is_route_resolved) {
            throw new \Exception("Route `$path` is not found", 404);
        }
        $controller = $this->factory->createController($request, $this->route);
    }
}