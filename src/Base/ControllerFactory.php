<?php

namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Interfaces\ControllerInterface;
use Explt13\Nosmi\Interfaces\DependencyManagerInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\ControllerFactoryInterface;
use Explt13\Nosmi\Interfaces\HttpFactoryInterface;

class ControllerFactory implements ControllerFactoryInterface
{
    private DependencyManagerInterface $dependency_manager;
    private HttpFactoryInterface $http_factory;
    public function __construct(DependencyManagerInterface $dependency_manager, HttpFactoryInterface $http_factory)
    {
        $this->http_factory = $http_factory;
        $this->dependency_manager = $dependency_manager;
    }
    public function createController(LightRouteInterface $route): ControllerInterface
    {
        /**
         * @var ControllerInterface $controller
         */
        $controller = $this->dependency_manager->getDependency($route->getController());
        $controller->setRoute($route);
        $controller->setResponse($this->http_factory->createResponse(200));
        $controller->setClient($this->http_factory->createClient());
        
        return $controller;
    }
}