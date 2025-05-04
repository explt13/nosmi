<?php

namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Interfaces\ControllerFactoryInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Interfaces\MiddlewareFactoryInterface;
use Explt13\Nosmi\Interfaces\RequestPipelineInterface;

class RequestPipeline implements RequestPipelineInterface
{
    private MiddlewareFactoryInterface $middleware_factory;
    private ControllerFactoryInterface $controller_factory;

    public function __construct(MiddlewareFactoryInterface $middleware_factory, ControllerFactoryInterface $controller_factory)
    {
        $this->middleware_factory = $middleware_factory;
        $this->controller_factory = $controller_factory;
    }

    public function process(LightServerRequestInterface $request, LightRouteInterface $route): LightResponseInterface
    {
        $controller = $this->controller_factory->createController($route);

        $middleware_registry = $this->middleware_factory->createRegistry();

        $middleware_dispatcher = $this->middleware_factory->createDispatcher($middleware_registry->getForRoute($route->getPath()), $controller);
        $response = $middleware_dispatcher->handle($request);
        return $response;
    }
}