<?php

namespace Explt13\Nosmi\Base;

use Exception;
use Explt13\Nosmi\Base\View;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Routing\Request;
use Explt13\Nosmi\Routing\RouteContext;

class ControllerResolver
{
    private ContainerInterface $container;
    private RouteContext $route;
    private Request $request;
    private View $view;
    
    public function __construct(RouteContext $route, ContainerInterface $container, Request $request, View $view)
    {
        $this->container = $container;
        $this->route = $route;
        $this->request = $request;
        $this->view = $view;
    }

    public function resolve()
    {
        $prefix = $this->route->prefix ? $this->route->prefix . '\\' : '';
        $controller = "Surfsail\\controllers\\" . $prefix . $this->route->controller . 'Controller';
        $controllerObject = $this->container->get($controller);
        $action = $this->lowerCamelCase($this->route->action) . "Action";
        if (method_exists($controllerObject, $action)) {
            $controllerObject->init($this->route, $this->request, $this->view);
            $controllerObject->$action();
        } else {
            throw new \Exception("Action: $controller::$action not found", 404);
        }
    }

    private function lowerCamelCase(string $str)
    {
        return lcfirst(str_replace("-", "", ucwords($str, '-')));
    }

}