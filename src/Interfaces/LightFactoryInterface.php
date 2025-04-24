<?php

namespace Explt13\Nosmi\Interfaces;

interface LightFactoryInterface
{
    /**
     * Creates a controller instance based on the given request and route.
     *
     * @param LightRequestInterface $request The request object.
     * @param LightRouteInterface $route The route object.
     * @return ControllerInterface The created controller instance.
     */
    public function createController(LightRequestInterface $request, LightRouteInterface $route): ControllerInterface;
        
    /**
     * Creates a request instance with the specified HTTP method and URI.
     *
     * @param string $method The HTTP method (e.g., GET, POST).
     * @param string $uri The URI of the request.
     * @return LightRequestInterface The created request instance.
     */
    public function createRequest(string $method, string $uri): LightRequestInterface;

    
    /**
     * Creates a response instance with the specified HTTP status code.
     *
     * @param int $status The HTTP status code (default is 200).
     * @return LightResponseInterface The created response instance.
     */
    public function createResponse(int $status = 200): LightResponseInterface;

    
    /**
     * Creates a view instance based on the given route.
     *
     * @param LightRouteInterface $route The route object.
     * @return ViewInterface The created view instance.
     */
    public function createView(LightRouteInterface $route): ViewInterface;

    
    /**
     * Creates a model instance.
     *
     * @return ModelInterface The created model instance.
     */
    public function createModel(): ModelInterface;
}