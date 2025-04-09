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
    private RouteContext $route_context;
    private Request $request;
    private View $view;
    
    public function __construct(RouteContext $route_context, ContainerInterface $container, Request $request, View $view)
    {
        $this->container = $container;
        $this->route_context = $route_context;
        $this->request = $request;
        $this->view = $view;
    }

    public function resolve()
    {
        $controller = "Surfsail\\controllers\\" . $this->route_context->prefix . $this->route_context->controller . 'Controller';
        $controllerObject = $this->container->get($controller);
        $action = $this->lowerCamelCase($this->route_context->action) . "Action";
        if (method_exists($controllerObject, $action)) {
            $controllerObject->init($this->route_context, $this->request, $this->view);
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