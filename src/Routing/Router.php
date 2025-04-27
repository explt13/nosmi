<?php
namespace Explt13\Nosmi\Routing;

use Explt13\Nosmi\Base\ControllerResolver;
use Explt13\Nosmi\Interfaces\MvcFactoryInterface;
use Explt13\Nosmi\Interfaces\LightRequestInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Middleware\FinalMiddleware;
use Explt13\Nosmi\Middleware\MiddlewareDispatcher;
use Explt13\Nosmi\Middleware\MiddlewareManager;
use Explt13\Nosmi\Middleware\MiddlewareRegistry;

class Router
{
    private MvcFactoryInterface $factory;
    private LightRouteInterface $route;

    public function __construct(
        MvcFactoryInterface $factory,
        LightRouteInterface $route,
    )
    {
        $this->factory = $factory;
        $this->route = $route;
    }

    public function dispatch(LightServerRequestInterface $request): void
    {
        if (empty($this->route::getRoutes())) {
            throw new \LogicException('No routes found, make sure you added them correctly.');
        }
        $path = $request->getUri()->getPath();
        $this->route = $this->route->resolvePath($path);

        $controller = $this->factory->createController($request, $this->route);
        $middleware_registry = MiddlewareRegistry::getInstance();
        $middleware_registry->addBulk($this->route->getRouteMiddleware());
        $middleware_dispatcher = new MiddlewareDispatcher($middleware_registry->getAll(), new FinalMiddleware());
    }
}