<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\Interfaces\CacheInterface;
use Explt13\Nosmi\Interfaces\ControllerInterface;
use Explt13\Nosmi\Interfaces\LightRequestInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Interfaces\ModelInterface;
use Explt13\Nosmi\Interfaces\ViewInterface;

abstract class Controller implements ControllerInterface
{
    private LightRouteInterface $route;
    private ViewInterface $view;
    protected LightServerRequestInterface $request;
    private LightResponseInterface $response;
    

    public function get()
    {
        $this->methodIsNotAvailable('GET');
    }

    public function post()
    {
        $this->methodIsNotAvailable('POST');
    }

    public function delete()
    {
        $this->methodIsNotAvailable('DELETE');
    }

    public function put()
    {
        $this->methodIsNotAvailable('PUT');
    }
    public function patch()
    {
        $this->methodIsNotAvailable('PATCH');
    }


    public function processRequest(LightServerRequestInterface $request)
    {
        $this->request = $request;
        if ($this->request->isAjax()) {
            $method = strtolower($this->request->getMethod());
            if (!method_exists($this, $method)) {
                throw new \RuntimeException("Method $method is not allowed.");
            }
            $this->$method();
        }
    }

    final public function setRoute(LightRouteInterface $route)
    {
        $this->route = $route;
    }

    final protected function getRoute(): LightRouteInterface
    {
        return $this->route;
    }

    final protected function getView(): ViewInterface
    {
        return $this->view;
    }

    private function methodIsNotAvailable(string $method)
    {
        throw new \RuntimeException("Method $method is not available for the route: {$this->route->getPath()}", 405);
    }
}