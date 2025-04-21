<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Interfaces\CacheInterface;
use Explt13\Nosmi\Interfaces\ControllerInterface;
use Explt13\Nosmi\Interfaces\LightRequestInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\ModelInterface;
use Explt13\Nosmi\Interfaces\ViewInterface;

abstract class Controller implements ControllerInterface
{
    protected LightRouteInterface $route;
    protected CacheInterface $cache;
    protected ViewInterface $view;
    protected LightRequestInterface $request;
    protected LightResponseInterface $response;
    protected ModelInterface $model; 
    

    public final function init(
        LightRequestInterface $request,
        LightRouteInterface $route,
        LightResponseInterface $response,
        ModelInterface $model,
        ViewInterface $view,
    ): void
    {
        $this->request = $request;
        $this->route = $route;
        $this->view =  $view;
        $this->response = $response;
        $this->model = $model;
    }
}