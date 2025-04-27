<?php

namespace Explt13\Nosmi\Interfaces;

interface MvcFactoryInterface
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