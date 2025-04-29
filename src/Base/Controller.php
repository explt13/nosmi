<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\AppConfig\AppConfig;
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
    private ?ViewInterface $view = null;
    protected LightServerRequestInterface $request;
    private LightResponseInterface $response;
    

    public function get(): LightResponseInterface
    {
        $this->methodIsNotAvailable('GET');
    }

    public function post(): LightResponseInterface
    {
        $this->methodIsNotAvailable('POST');
    }

    public function delete(): LightResponseInterface
    {
        $this->methodIsNotAvailable('DELETE');
    }

    public function put(): LightResponseInterface
    {
        $this->methodIsNotAvailable('PUT');
    }
    public function patch(): LightResponseInterface
    {
        $this->methodIsNotAvailable('PATCH');
    }

    public function renderPage(): LightResponseInterface
    {
        throw new \RuntimeException("Expected renderPage() method existence for non-AJAX request.");
    }


    public function processRequest(LightServerRequestInterface $request): LightResponseInterface
    {
        $this->request = $request;
        if ($this->request->isAjax()) {
            $method = strtolower($this->request->getMethod());
            if (!method_exists($this, $method)) {
                throw new \RuntimeException("Method $method is not allowed.");
            }
            return $this->$method();
        } else {
            $renderMethod = "render" . ucfirst($this->route->getRender()) . "Page";
            return $this->$renderMethod();
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
        if (is_null($this->view)) {
            $this->view = new View(AppConfig::getInstance());
        } 
        return $this->view;
    }

    private function methodIsNotAvailable(string $method)
    {
        throw new \RuntimeException("Method $method is not available for the route: {$this->route->getPath()}", 405);
    }
}