<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Interfaces\ControllerInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Interfaces\ReadExchangeInterface;
use Explt13\Nosmi\Interfaces\ViewInterface;

abstract class Controller implements ControllerInterface
{
    private LightRouteInterface $route;
    private ?ViewInterface $view = null;
    protected LightServerRequestInterface $request;
    private LightResponseInterface $response;
    

    /**
     * Handles a GET request.
     * 
     * This method must be implemented in derived classes to handle
     * __GET__ request. If not implemented, an exception will be thrown.
     * 
     * @return LightResponseInterface
     * @throws \RuntimeException If the method is not implemented.
     */
    protected function get(): LightResponseInterface
    {
        $this->methodIsNotAvailable('GET');
    }

    /**
     * Handles a POST request.
     * 
     * This method must be implemented in derived classes to handle
     * __POST__ request. If not implemented, an exception will be thrown.
     * 
     * @return LightResponseInterface
     * @throws \RuntimeException If the method is not implemented.
     */
    protected function post(): LightResponseInterface
    {
        $this->methodIsNotAvailable('POST');
    }

    /**
     * Handles a DELETE request.
     * 
     * This method must be implemented in derived classes to handle
     * __DELETE__ request. If not implemented, an exception will be thrown.
     * 
     * @return LightResponseInterface
     * @throws \RuntimeException If the method is not implemented.
     */
    protected function delete(): LightResponseInterface
    {
        $this->methodIsNotAvailable('DELETE');
    }

    /**
     * Handles a PUT request.
     * 
     * This method must be implemented in derived classes to handle
     * __PUT__ request. If not implemented, an exception will be thrown.
     * 
     * @return LightResponseInterface
     * @throws \RuntimeException If the method is not implemented.
     */
    protected function put(): LightResponseInterface
    {
        $this->methodIsNotAvailable('PUT');
    }

    /**
     * Handles a PATCH request.
     * 
     * This method must be implemented in derived classes to handle
     * __PATCH__ request. If not implemented, an exception will be thrown.
     * 
     * @return LightResponseInterface
     * @throws \RuntimeException If the method is called.
     */
    protected function patch(): LightResponseInterface
    {
        $this->methodIsNotAvailable('PATCH');
    }

    /**
     * @return LightResponseInterface&ReadExchangeInterface&WriteExchangeInterface
     */
    public function processRequest(LightServerRequestInterface $request): LightResponseInterface
    {
        $this->request = $request;
        if ($this->request->isAjax()) {
            $method = strtolower($this->request->getMethod());
            if (!method_exists($this, $method)) {
                throw new \RuntimeException("Route {$this->route->getPath()} does not have $method method.");
            }
            return $this->$method();
        } else {
            $action = $this->route->getAction();
            if (is_null($action)) {
                throw new \RuntimeException("Route {$this->route->getPath()} does not have an action. If the route is not assumed to be an API only, provide an action in Route::add method.");
            }
            $method = $action . "Action";
            if (!method_exists($this, $method)) {
                throw new \RuntimeException("Expected controller {$this->route->getController()} to have $method method.");
            }
            return $this->$method();
        }
    }

    /**
     * Sets the route for the controller.
     *
     * @param LightRouteInterface $route The route to be set.
     */
    final public function setRoute(LightRouteInterface $route)
    {
        $this->route = $route;
    }
     
    /**
     * Retrieves the current route associated with the controller.
     *
     * @return LightRouteInterface The current route.
     */
    final protected function getRoute(): LightRouteInterface
    {
        return $this->route;
    }
     
    /**
     * Retrieves the view instance associated with the controller.
     * If the view instance is not already created, it initializes a new one.
     *
     * @return ViewInterface The view instance.
     */
    final protected function getView(): ViewInterface
    {
        if (is_null($this->view)) {
            $this->view = new View(AppConfig::getInstance());
        } 
        return $this->view;
    }
     
    /**
     * Throws an exception indicating that the specified method is not available for the current route.
     *
     * @param string $method The name of the unavailable method.
     *
     * @throws \RuntimeException Always throws an exception with a 405 status code.
     */
    private function methodIsNotAvailable(string $method)
    {
        throw new \RuntimeException("Method $method is not available for the route: {$this->route->getPath()}", 405);
    }
}