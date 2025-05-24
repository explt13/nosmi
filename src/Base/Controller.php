<?php
namespace Explt13\Nosmi\Base;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Http\Client;
use Explt13\Nosmi\Interfaces\ControllerInterface;
use Explt13\Nosmi\Interfaces\ExchangeInterface;
use Explt13\Nosmi\Interfaces\LightClientInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Interfaces\ReadExchangeInterface;
use Explt13\Nosmi\Interfaces\ViewInterface;
use Explt13\Nosmi\Interfaces\WriteExchangeInterface;

abstract class Controller implements ControllerInterface
{
    private LightRouteInterface $route;
    private ViewInterface $view;
    private Client $client;
    protected LightServerRequestInterface $request;
    protected LightResponseInterface $response;

    /**
     * Handles a GET request.
     * 
     * This method must be implemented in derived classes to handle
     * __GET__ request. If not implemented, an exception will be thrown.
     * 
     * @throws \RuntimeException If the method is not implemented.
     */
    protected function get()
    {
        $this->methodIsNotAvailable('GET');
    }

    /**
     * Handles a POST request.
     * 
     * This method must be implemented in derived classes to handle
     * __POST__ request. If not implemented, an exception will be thrown.
     * 
     * @throws \RuntimeException If the method is not implemented.
     */
    protected function post()
    {
        $this->methodIsNotAvailable('POST');
    }

    /**
     * Handles a DELETE request.
     * 
     * This method must be implemented in derived classes to handle
     * __DELETE__ request. If not implemented, an exception will be thrown.
     * 
     * @throws \RuntimeException If the method is not implemented.
     */
    protected function delete()
    {
        $this->methodIsNotAvailable('DELETE');
    }

    /**
     * Handles a PUT request.
     * 
     * This method must be implemented in derived classes to handle
     * __PUT__ request. If not implemented, an exception will be thrown.
     * 
     * @throws \RuntimeException If the method is not implemented.
     */
    protected function put()
    {
        $this->methodIsNotAvailable('PUT');
    }

    /**
     * Handles a PATCH request.
     * 
     * This method must be implemented in derived classes to handle
     * __PATCH__ request. If not implemented, an exception will be thrown.
     * 
     * @throws \RuntimeException If the method is called.
     */
    protected function patch()
    {
        $this->methodIsNotAvailable('PATCH');
    }

    public function processRequest(LightServerRequestInterface $request): LightResponseInterface
    {
        $this->request = $request;
        if ($this->request->isAjax()) {
            $method = strtolower($this->request->getMethod());
            if (!method_exists($this, $method)) {
                throw new \RuntimeException("Route {$this->route->getPath()} does not have $method method.");
            }
            $this->$method();
            return $this->response;
        } else {
            $action = $this->route->getAction();
            if (is_null($action)) {
                throw new \RuntimeException("Route {$this->route->getPath()} does not have an action. If the route is not assumed to be an API only, provide an action in Route::add method.");
            }
 
            $method = strtolower($action) . "Action";
            if (!method_exists($this, $method)) {
                throw new \RuntimeException("Expected controller {$this->route->getController()} to have $method method.");
            }
            $this->$method();
            return $this->response;
        }
    }


    final public function setRoute(LightRouteInterface $route): void
    {
        $this->route = $route;
    }

    final public function setResponse(LightResponseInterface $response): void
    {
        $this->response = $response;
    }

    final public function setClient(LightClientInterface $client): void
    {
        $this->client = $client;
    }

    final protected function getClient(): LightClientInterface
    {
        return $this->client;
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
     *
     * @return ViewInterface The view instance.
     */
    final protected function getView(): ViewInterface
    {
        $this->view = (new View(AppConfig::getInstance()))->withRoute($this->route);
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