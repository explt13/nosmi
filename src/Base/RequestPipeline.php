<?php

namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Middleware\MiddlewareFactory;

class RequestPipeline
{
    private MiddlewareFactory $middleware_factory;

    public function __construct(MiddlewareFactory $middleware_factory)
    {
        $this->middleware_factory = $middleware_factory;
    }

    public function process(LightServerRequestInterface $request, LightRouteInterface $route): LightResponseInterface
    {
        $controller = new $route->getController();
        $controller->setRoute($route);
    
        $middleware_registry = $this->middleware_factory->createRegistry();
        $middleware_registry->addBulk($route->getRouteMiddleware());

        $middleware_dispatcher = $this->middleware_factory->createDispatcher($middleware_registry->getAll(), $controller);
        $response = $middleware_dispatcher->handle($request);
        return $response;
    }
}