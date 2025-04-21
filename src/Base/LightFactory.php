<?php

namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\ControllerInterface;
use Explt13\Nosmi\Interfaces\DependencyManagerInterface;
use Explt13\Nosmi\Interfaces\LightFactoryInterface;
use Explt13\Nosmi\Interfaces\LightRequestInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\ModelInterface;
use Explt13\Nosmi\Interfaces\ViewInterface;
use Explt13\Nosmi\Routing\Request;
use Explt13\Nosmi\Routing\Response;

class LightFactory implements LightFactoryInterface
{
    private DependencyManagerInterface $dependency_manager;
    public function __construct(DependencyManagerInterface $dependency_manager)
    {
        $this->dependency_manager = $dependency_manager;        
    }
    public function createController(LightRequestInterface $request, LightRouteInterface $route): ControllerInterface
    {
        /**
         * @var ControllerInterface $controller
         */
        $controller = $this->dependency_manager->getDependency($route->getController());
        
        $controller->init(
            $request,
            $route,
            $this->createResponse(),
            $this->createModel(),
            $this->createView($route),
        );

        return $controller;
    }

    public function createResponse(int $status = 200): LightResponseInterface
    {
        return new Response($status);
    }

    public function createModel(): ModelInterface
    {
        return new Model();
    }

    public function createRequest(string $method, string $uri): LightRequestInterface
    {
        return new Request($method, $uri);
    }

    public function createView(LightRouteInterface $route): ViewInterface
    {
        $config = $this->dependency_manager->getDependency(ConfigInterface::class);
        return (new View($config))->withRoute($route);
    }
}